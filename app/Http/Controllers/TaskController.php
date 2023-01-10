<?php

namespace App\Http\Controllers;

use App\Task;
use App\Board;
use Exception;
use App\Models\User;
use App\Models\Report;
use App\Models\Project;
use App\Models\TaskMember;
use App\Models\Notification;
use App\Models\TaskProgress;
use Illuminate\Http\Request;
use App\Models\BoardProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TaskResource;
use App\Models\Subtask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index($user_id)
    {
        try {
            //  fetch all tasks based on current user id
            $project = Project::findOrFail(request()->segment(4));
            $findProject = Project::all()->where('project_title', $project->project_title);
            $project_id = [];
            foreach ($findProject as $item) {
                $project_id[] = $item->project_id;
            }

            $tasks = Task::whereIn('project_id', $project_id)->get();

            //  return tasks as a resource
            return TaskResource::collection($tasks);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $user_id
     * @return JsonResponse|TaskResource|void
     */
    public function store(Request $request, $user_id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'board_id' => 'required',
                'name' => 'required|max:255|regex:/^[\s\w-]*$/',
                'description' => 'required|regex:/^[\s\w-]*$/',
                'privacy_status' => 'required'
            ]);
            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {

                $project = Project::where('project_id', request()->segment(4))->first();
                $id = Project::where('project_title', $project->project_title)->get();

                //  store new task in the database
                $task = new Task;
                $task->user_id = $user_id;
                $task->project_id = request()->segment(4);
                $task->board_id = $request->input('board_id');
                $task->name = $request->input('name');
                $task->description = $request->input('description');
                $task->privacy_status = $request->input('privacy_status');

                $userName = User::firstWhere('id', $user_id);

                // Notify Admin
                Notification::create([
                    'user_id' => 1,
                    'notification_message' => $userName->name . ' created a task: ' . $request->input('name') . ' in ' . $project->project_title,
                ]);

                Report::create(['user_id' => $user_id, 'project_id' => $request->segment(4),  'message' => ' created a task: ' . $request->input('name') . ' in ' . $project->project_title]);
                if ($task->save()) {
                    TaskProgress::create([
                        'board_id' => $request->input('board_id'),
                        'task_id' => $task->id,
                        'total_subtask' => 0,
                        'total_subtask_done' => 0
                    ]);

                    $totalTaskDone = TaskProgress::where('board_id', $request->input('board_id'))
                        ->whereColumn('total_subtask', 'total_subtask_done')->where('total_subtask_done', '>', 0)
                        ->where('total_subtask', '>', 0)
                        ->get()->count();

                    // Update Board Progress
                    BoardProgress::where('board_id', $request->input('board_id'))
                        ->update([
                            'board_id' => $request->input('board_id'),
                            'total_task' => DB::table('task_progress')
                                ->where('board_id', $request->input('board_id'))
                                ->get()
                                ->count(),
                            'total_task_done' => $totalTaskDone
                        ]);

                    // Notify users
                    foreach ($id as $notify) {
                        $user = User::withTrashed()->where('id', $notify->id)->first();
                        Notification::create([
                            'user_id' => $user->id,
                            'notification_message' => $userName->name . ' created a task: ' . $request->input('name') . ' in ' . $project->project_title,
                        ]);
                    }

                    //  if saved then return task as a resource
                    return new TaskResource($task);
                }
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @param $user_id
     * @return TaskResource
     */
    public function show($id, $user_id)
    {
        try {
            //  fetch task based on current user id and task id
            $project = Project::findOrFail(request()->segment(5));
            $findProject = Project::all()->where('project_title', $project->project_title);
            $project_id = [];
            foreach ($findProject as $item) {
                $project_id[] = $item->project_id;
            }

            $task = Task::whereIn('project_id', $project_id)->findOrFail($id);

            //  return task as a resource
            return new TaskResource($task);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Task $task
     * @return void
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * 
     * 
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $user_id
     * @return JsonResponse|TaskResource|void
     */

    public function update(Request $request, $user_id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'board_id' => 'required',
                'name' => 'required|regex:/^[\s\w-]*$/|max:255',
                'description' => 'required|regex:/^[\s\w-]*$/',
            ]);

            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                //  update existing task data based on current user id and task id
                $project = Project::findOrFail(request()->segment(4));
                $findProject = Project::all()->where('project_title', $project->project_title);
                $project_id = [];
                foreach ($findProject as $item) {
                    $project_id[] = $item->project_id;
                }

                $task = Task::whereIn('project_id', $project_id)
                    ->findOrFail($request->task_id);

                $old_board_id = $task->board_id;

                // Update Task
                // $task->user_id = $user_id;
                $task->board_id = $request->input('board_id');
                $task->name = $request->input('name');
                $task->description = $request->input('description');
                $task->task_start_date = $request->task_start_date;
                $task->task_due_date = $request->task_due_date;
                $task->privacy_status = $request->privacy_status;

                $userName = User::firstWhere('id', $user_id);

                if ($task->save()) {
                    if ($old_board_id != $task->board_id) {
                        $totalSubtask = Subtask::where('task_id', $task->id)->get()->count();
                        $totalSubtaskDone = Subtask::where('task_id', $task->id)
                            ->where('board_id', 2)
                            ->get()->count();
                        TaskProgress::where('task_id', $task->id)->where('board_id', $old_board_id)
                            ->update(['board_id' => $task->board_id,'total_subtask' => $totalSubtask, 'total_subtask_done' => $totalSubtaskDone]);

                        $totalTaskDone = TaskProgress::where('board_id', $task->board_id)
                            ->whereColumn('total_subtask', 'total_subtask_done')
                            ->where('total_subtask', '>', 0)
                            ->where('total_subtask_done', '>', 0)
                            ->get()->count();
                        $totalTask = Task::where('board_id', $task->board_id)->get()->count();
                        BoardProgress::where('board_id', $task->board_id)->update([
                            'total_task' => $totalTask,
                            'total_task_done' => $totalTaskDone
                        ]);

                        $totalOldTaskDone = TaskProgress::where('board_id', $old_board_id)
                            ->whereColumn('total_subtask', 'total_subtask_done')
                            ->where('total_subtask', '>', 0)
                            ->where('total_subtask_done', '>', 0)
                            ->get()->count();
                        $totalOldTask = Task::where('board_id', $old_board_id)->get()->count();
                        BoardProgress::where('board_id', $old_board_id)->update([
                            'total_task' => $totalOldTask,
                            'total_task_done' => $totalOldTaskDone
                        ]);
                    }
                    Report::create(['user_id' => $user_id, 'project_id' => request()->segment(4), 'message' => ' updated the task in ' . $project->project_title]);
                    // Notify all users in the project that a task is updated
                    foreach ($findProject as $notify) {
                        $user = User::withTrashed()->where('id', $notify->id)->first();
                        if (Auth::id() != $user->id) {
                            Notification::create([
                                'user_id' => $user->id,
                                'notification_message' =>  $userName->name . ' updated the task in ' . $project->project_title,
                            ]);
                        }
                    }
                    //  if saved then return task as a resource
                    return new TaskResource($task);
                }
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @param $user_id
     * @return TaskResource|void
     */
    public function destroy($id, $user_id)
    {
        try {
            $project = Project::findOrFail(request()->segment(5));
            $findProject = Project::all()->where('project_title', $project->project_title);
            $project_id = [];
            foreach ($findProject as $item) {
                $project_id[] = $item->project_id;
            }

            $task = Task::whereIn('project_id', $project_id)->findOrFail($id);
            $projectHead = Project::join('users', 'users.id', '=', 'projects.id')
                ->where('project_title', $project->project_title)
                ->where('projects.is_project_head', '=', 1)
                ->where('users.id', '!=', 1)
                ->first();

            $taskMember = TaskMember::where('task_id', $id)->get();


            $user = User::firstWhere('id', $user_id);

            // Notify Admin
            Notification::create([
                'user_id' => 1,
                'notification_message' => $user->name . ' deleted a task: ' . $task->name . ' in ' . $project->project_title,
            ]);

            // Notify Project Head
            Notification::create([
                'user_id' => $projectHead->id,
                'notification_message' => $user->name . ' deleted a task: ' . $task->name . ' in ' . $project->project_title,
            ]);

            // Notify Task Members
            foreach ($taskMember as $notify) {
                if ($user->id != $notify->user_id) {
                    Notification::create([
                        'user_id' => $notify->user_id,
                        'notification_message' => $user->name . ' deleted a task: ' . $task->name . ' in ' . $project->project_title,
                        'url' => ''
                    ]);
                }
            }
            $board = $task->board_id;
            Report::create(['user_id' => $user_id, 'project_id' => request()->segment(5), 'message' => ' deleted a task: ' . $task->name . ' in ' . $project->project_title]);
            if ($task->delete()) {
                $totalTaskDone = TaskProgress::where('board_id', $board)
                    ->whereColumn('total_subtask', 'total_subtask_done')->where('total_subtask_done', '>', 0)
                    ->get()->count();

                BoardProgress::where('board_id', $board)
                    ->update([
                        'board_id' => $board,
                        'total_task' => DB::table('task_progress')
                            ->where('board_id', $board)
                            ->get()
                            ->count(),
                        'total_task_done' => $totalTaskDone
                    ]);
                //  if deleted then return task as a resource
                return new TaskResource($task);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /* Assigned the user to task */
    public function assignTaskMember(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'members' => 'required',
            ]);

            $url = $this->prev_segments(url()->previous());
            $getId = null;
            for ($i = 0; $i < count($url); $i++) {
                if ($i == count($url) - 1) {
                    $getId = $url[$i];
                }
            }

            $members = $request->input('members');

            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                $task = Task::findOrFail($id);
                $userAssignedTask = DB::table('task_members')
                    ->join('users', 'users.id', '=', 'task_members.user_id')
                    ->join('tasks', 'tasks.id', '=', 'task_members.task_id')
                    ->where('tasks.project_id', $task->project_id)
                    ->where('task_id', $task->id)
                    ->select('users.name as full_name', 'task_members.user_id')
                    ->get();

                $currentMembersTask = [];

                foreach ($userAssignedTask as $assigned) {
                    $currentMembersTask[] = $assigned->user_id;
                }

                // Assign member/s to task
                foreach ($members as $new_member_task) {
                    if (!in_array($new_member_task, $currentMembersTask)) {
                        TaskMember::create([
                            'task_id' => $task->id,
                            'user_id' => $new_member_task,
                            'project_uuid' => $getId
                        ]);

                        Report::create(['user_id' => $new_member_task, 'project_id' => $task->project_id, 'message' => ' is assigned to a task']);
                        $user = User::where('id', $new_member_task)->first();
                        Notification::create([
                            'user_id' => $user->id,
                            'notification_message' => 'You have been assigned in the task: ' . $task->name,
                        ]);
                    }
                }

                // Remove member/s to task
                foreach ($currentMembersTask as $currentMembers) {
                    if (!in_array($currentMembers, $members)) {
                        $user = User::where('id', $currentMembers)->first();
                        Notification::create([
                            'user_id' => $user->id,
                            'notification_message' => 'You have been removed in the task:' . $task->name,
                        ]);

                        Report::create(['user_id' => $currentMembers, 'project_id' => $task->project_id, 'message' => ' is removed in a task']);

                        TaskMember::where('task_id', $task->id)
                            ->where('user_id', $currentMembers)
                            ->delete();
                    }
                }
                return response()->json($currentMembersTask);
            }
        } catch (Exception $e) {
            dd($e);
            abort_if($e, 500);
        }
    }

    /* Fetch the assigned user in task */
    public function assignedTaskMember($id)
    {
        try {
            $userAssignedTask = DB::table('task_members')
                ->join('users', 'users.id', '=', 'task_members.user_id')
                ->join('tasks', 'tasks.id', '=', 'task_members.task_id')
                ->where('task_members.task_id', $id)
                ->select('users.name as full_name', 'task_members.user_id')
                ->get();

            return response()->json($userAssignedTask);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }


    public function taskPrivacy($id)
    {
        try {
            $taskMember = DB::table('task_members')
                ->join('tasks', 'tasks.id', '=', 'task_members.task_id')
                ->where('task_members.task_id', $id)
                ->where('task_members.user_id', Auth::user()->id)
                ->get(['task_members.user_id as user_id', 'task_members.task_id as task_id', 'tasks.privacy_status as privacy'])
                ->first();

            return response()->json($taskMember);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function prev_segments($uri)
    {
        $segments = explode('/', str_replace('' . url('') . '', '', $uri));

        return array_values(array_filter($segments, function ($value) {
            return $value !== '';
        }));
    }
}
