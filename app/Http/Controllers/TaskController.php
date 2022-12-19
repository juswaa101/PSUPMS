<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Report;
use App\Models\TaskMember;
use App\Models\User;
use App\Task;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
                'name' => 'required|max:255',
                'description' => 'required|max:255',
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
                    // Notify users
                    foreach ($id as $notify) {
                        $user = User::where('id', $notify->id)->first();
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
                'name' => 'required',
                'description' => 'required',
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
    
                // Update Task
                $task->user_id = $user_id;
                $task->board_id = $request->input('board_id');
                $task->name = $request->input('name');
                $task->description = $request->input('description');
                $task->task_start_date = $request->task_start_date;
                $task->task_due_date = $request->task_due_date;
                $task->privacy_status = $request->privacy_status;
    
                $userName = User::firstWhere('id', $user_id);
    
                if ($task->save()) {
                    Report::create(['user_id' => $user_id, 'project_id' => request()->segment(4), 'message' => ' updated the task in ' . $project->project_title]);
                    // Notify all users in the project that a task is updated
                    foreach ($findProject as $notify) {
                        $user = User::where('id', $notify->id)->first();
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

            // Dito yung last na aayusin null yun value in auth
            dd(Auth::user());

            // Notify Admin
            Notification::create([
                'user_id' => 1,
                'notification_message' => Auth::user()->name . ' deleted a task: ' . $task->name . ' in ' . $project->project_title,
            ]);

            // Notify Project Head
            Notification::create([
                'user_id' => $projectHead->id,
                'notification_message' => Auth::user()->name . ' deleted a task: ' . $task->name . ' in ' . $project->project_title,
            ]);

            // Notify Task Members
            foreach ($taskMember as $notify) {
                if (Auth::user()->id != $notify->user_id) {
                    Notification::create([
                        'user_id' => $notify->user_id,
                        'notification_message' => Auth::user()->name . ' deleted a task: ' . $task->name . ' in ' . $project->project_title,
                        'url' => ''
                    ]);
                }
            }

            Report::create(['user_id' => $user_id, 'project_id' => request()->segment(5), 'message' => ' deleted a task: ' . $task->name . ' in ' . $project->project_title]);

            if ($task->delete()) {
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
    
            $members = $request->input('members');
    
            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                $task = Task::findOrFail($id);
                $userAssignedTask = DB::table('task_members')
                    ->join('users', 'users.id', '=', 'task_members.user_id')
                    ->join('tasks', 'tasks.id', '=', 'task_members.task_id')
                    ->where('tasks.project_id', $task->project_id)
                    ->select('users.name as full_name', 'task_members.user_id')
                    ->get();
    
                $project = Project::join('tasks', 'tasks.project_id', '=', 'projects.project_id')
                    ->where('tasks.project_id', $task->id)->first();
    
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

    public function taskProgress($id)
    {
        try {
            $totalCountSubtask = DB::table('subtasks')->where('task_id', $id)->get()->count();
            $totalCompletedSubtask = DB::table('subtasks')->where('task_id', $id)->where('board_id', 2)->get()->count();
            return response()->json([
                'totalCountSubtask' => $totalCountSubtask,
                'totalCountCompletedSubtask' => $totalCompletedSubtask
            ]);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
