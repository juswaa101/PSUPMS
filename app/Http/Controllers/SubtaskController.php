<?php

namespace App\Http\Controllers;

use App\Task;
use Exception;
use App\Models\User;
use App\Models\Report;
use App\Models\Project;
use App\Models\Subtask;
use App\Models\TaskMember;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SubtaskResource;
use App\Models\BoardProgress;
use App\Models\TaskProgress;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class SubtaskController extends Controller
{
    public function index($task_id, $user_id)
    {
        try {
            $task = Task::where('id', $task_id)->firstOrFail();
            if ($task->privacy_status == 1 && Auth::user()->role != "admin") {
                $subtask = Subtask::where('user_id', $user_id)
                    ->where('task_id', $task_id)->get();

                return SubtaskResource::collection($subtask);
            } else {
                // Fetch subtask per each user that is assigned in the task
                $subtask = Subtask::where('task_id', $task_id)->get();
                return SubtaskResource::collection($subtask);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function show($id, $task_id, $user_id)
    {
        try {
            // Fetch subtask per each user that is assigned in the task
            $task = Task::where('id', $task_id)->firstOrFail();
            if ($task->privacy_status == 1 && Auth::user()->role != "admin") {
                $subtask = Subtask::where('user_id', $user_id)
                    ->where('task_id', $task_id)
                    ->findOrFail($id);

                // return subtask as a resource
                return new SubtaskResource($subtask);
            } else {
                $subtask = Subtask::findOrFail($id);
                return new SubtaskResource($subtask);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'subtask_name' => 'required|max:255|regex:/^[\s\w-]*$/',
                'subtask_description' => 'required|max:255|regex:/^[\s\w-]*$/',
            ]);

            // If validator fails, throw bad request, otherwise store it in database
            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                // Store Subtask
                $subtask = Subtask::create([
                    'board_id' => $request->input('board_id'),
                    'user_id' => auth()->user()->id,
                    'task_id' => $request->input('task_id'),
                    'subtask_name' => $request->input('subtask_name'),
                    'subtask_description' => $request->input('subtask_description')
                ]);

                $subtaskJoin = Subtask::join('task_progress', 'task_progress.task_id', 'subtasks.task_id')
                    ->firstWhere('task_progress.task_id', $request->input('task_id'));

                $task = Task::firstWhere('id', $request->input('task_id'));
                $project = Project::firstWhere('project_id', $task->project_id);

                $totalSubtask = Subtask::where('task_id', $request->input('task_id'))->get()->count();
                TaskProgress::where('task_id', $request->input('task_id'))
                    ->update(['total_subtask' => $totalSubtask]);

                $totalTaskDone = TaskProgress::where('board_id', $subtaskJoin->board_id)
                    ->whereColumn('total_subtask', 'total_subtask_done')
                    ->where('total_subtask_done', '>', 0)
                    ->where('total_subtask', '>', 0)
                    ->get()->count();

                BoardProgress::where('board_id', $subtaskJoin->board_id)->update([
                    'total_task_done' => $totalTaskDone
                ]);

                Report::create(['user_id' => auth()->user()->id, 'project_id' => $task->project_id, 'message' => ' created a subtask in' . $task->name]);


                $users = TaskMember::where('task_id', '=', $request->input('task_id'))->get();
                foreach ($users as $user) {
                    Notification::create([
                        'user_id' => $user->id,
                        'notification_message' =>  Auth::user()->name . ' created a subtask: ' . $request->input('subtask_name') . ' in ' . $task->name . ' in ' . $project->project_title,
                    ]);
                }

                return new SubtaskResource($subtask);
            }
        } catch (Exception $e) {
            dd($e);
            abort_if($e, 500);
        }
    }

    public function destroy($id)
    {
        try {
            $subtask = Subtask::where('id', $id)->findOrFail($id);

            $task = Task::firstWhere('id', $subtask->task_id);

            $users = TaskMember::where('task_id', '=', $subtask->task_id)->get();
            $project = Project::firstWhere('project_id', $task->project_id);

            $task_id = $subtask->task_id;

            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->user_id,
                    'notification_message' =>  Auth::user()->name . ' deleted a subtask: ' . $subtask->subtask_name . ' in ' . $task->name . ' in ' . $project->project_title,
                ]);
            }

            Report::create(['user_id' => auth()->user()->id, 'project_id' => $task->project_id, 'message' => ' deleted a subtask in ' . $task->name]);

            $subtask->delete();

            $totalSubtask = Subtask::where('task_id', $task_id)->get()->count();
            $totalSubtaskDone = Subtask::where('task_id', $task_id)
                ->where('board_id', 2)
                ->get()->count();
            TaskProgress::where('task_id', $task_id)
                ->update(['total_subtask' => $totalSubtask, 'total_subtask_done' => $totalSubtaskDone]);

            $totalTaskDone = TaskProgress::where('board_id', $task->board_id)
                ->whereColumn('total_subtask', 'total_subtask_done')
                ->where('total_subtask', '>', 0)
                ->where('total_subtask_done', '>', 0)
                ->get()->count();
            BoardProgress::where('board_id', $task->board_id)->update([
                'total_task_done' => $totalTaskDone
            ]);

            return new SubtaskResource($subtask);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }


    public function update(Request $request, $id, $task_id, $user_id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'subtask_name' => 'required|max:255|regex:/^[\s\w-]*$/',
                'subtask_description' => 'required|max:255|regex:/^[\s\w-]*$/',
            ]);

            // Check Validations for Update
            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                $task = Task::where('id', $task_id)->firstOrFail();
                if ($task->privacy_status == 1 && Auth::user()->role != "admin") {
                    $subtask = Subtask::where('user_id', $user_id)
                        ->where('task_id', $task_id)->findOrFail($id);

                    $notifyAllSubtaskUser = Subtask::where('task_id', $task_id)->get();
                    $oldBoard = "";
                    $oldBoardId = $subtask->board_id;
                    if($subtask->board_id == 0) 
                        $oldBoard = 'To do';
                    else if ($subtask->board_id == 1)
                        $oldBoard = 'In Progress';
                    else
                        $oldBoard = 'Done';
                    
                    // Update Subtask
                    $subtask->board_id = $request->input('board_id');
                    $subtask->subtask_name = $request->input('subtask_name');
                    $subtask->subtask_description = $request->input('subtask_description');

                    $project = Task::firstWhere('id', $subtask->task_id);
                    $projectFirst = Project::firstWhere('id', $project->id);

                    if ($subtask->save()) {
                        if($oldBoardId != $subtask->board_id) {
                            $newBoardName = "";
                            $task = Task::firstWhere('id', $task_id);
                            if($subtask->board_id == 0) 
                                $newBoardName = 'To do';
                            else if($subtask->board_id == 1)
                                $newBoardName = 'In Progress';
                            else
                                $newBoardName = 'Done';

                            Report::create(['user_id' => $user_id, 'project_id' => $project->project_id, 'message' => ' moved from ' . $oldBoard . ' to ' . $newBoardName . ' in task: ' . $task->name . ' in project: ' . $projectFirst->project_title]);
                            // Notify all users in the project that a task is updated
                            foreach ($notifyAllSubtaskUser as $notify) {
                                $user = User::withTrashed()->where('id', $notify->id)->first();
                                if (Auth::id() != $user->id) {
                                    Notification::create([
                                        'user_id' => $user->id,
                                        'notification_message' =>  auth()->user()->name . ' moved from ' . $oldBoard . ' to ' . $newBoardName . ' in task: ' . $task->name . ' in project: ' . $projectFirst->project_title,
                                    ]);
                                }
                            }
                        }
                        else { 
                            $task = Task::firstWhere('id', $task_id);
                            Report::create(['user_id' => $user_id, 'project_id' => $task->project_id, 'message' => ' updated a subtask in ' . $task->name . ' in project: ' . $projectFirst->project_title]);

                            // Notify all users in the task that a subtask is updated
                            foreach ($notifyAllSubtaskUser as $notify) {
                                $user = User::where('id', $notify->user_id)->first();
                                Notification::create([
                                    'user_id' => $user->id,
                                    'notification_message' =>  $user->name . ' updated a subtask in ' . $task->name . ' in project:' . $projectFirst->project_title,
                                ]);
                            }
                        }
                        $totalSubtaskDone = Subtask::where('task_id', $task_id)->where('board_id', 2)
                            ->where('is_approved', 1)
                            ->get()->count();
                        TaskProgress::where('task_id', $task_id)
                            ->update(['total_subtask_done' => $totalSubtaskDone]);

                        $task = Subtask::join('tasks', 'tasks.id', '=', 'subtasks.task_id')
                            ->firstWhere('subtasks.task_id', $task_id);
                        $totalTaskDone = TaskProgress::where('board_id', $task->board_id)
                            ->whereColumn('total_subtask', 'total_subtask_done')
                            ->where('total_subtask', '>', 0)
                            ->where('total_subtask_done', '>', 0)
                            ->get()->count();
                        BoardProgress::where('board_id', $task->board_id)->update([
                            'total_task_done' => $totalTaskDone
                        ]);

                        

                        return new SubtaskResource($subtask);
                    }
                } else {
                    // Fetch subtask per each user that is assigned in the task
                    $subtask = Subtask::findOrFail($id);
                    $project = Task::firstWhere('id', $subtask->task_id);
                    $projectFirst = Project::firstWhere('id', $project->id);

                    $notifyAllSubtaskUser = Subtask::where('task_id', $task_id)->get();
                    $oldBoard = "";
                    $oldBoardId = $subtask->board_id;
                    if($subtask->board_id == 0) 
                        $oldBoard = 'To do';
                    else if ($subtask->board_id == 1)
                        $oldBoard = 'In Progress';
                    else
                        $oldBoard = 'Done';

                    // Update Subtask
                    $subtask->board_id = $request->input('board_id');
                    $subtask->subtask_name = $request->input('subtask_name');
                    $subtask->subtask_description = $request->input('subtask_description');

                    $project = Task::firstWhere('id', $subtask->task_id);

                    if ($subtask->save()) {
                        if($oldBoardId != $subtask->board_id) {
                            $newBoardName = "";
                            if($subtask->board_id == 0) 
                                $newBoardName = 'To do';
                            else if ($subtask->board_id == 1)
                                $newBoardName = 'In Progress';
                            else
                                $newBoardName = 'Done';
                            $task = Task::firstWhere('id', $task_id);

                            Report::create(['user_id' => $user_id, 'project_id' => $project->project_id, 'message' => ' moved from ' . $oldBoard . ' to ' . $newBoardName . ' in task: ' . $task->name . ' in project: ' . $projectFirst->project_title]);
                            // Notify all users in the project that a task is updated
                            foreach ($notifyAllSubtaskUser as $notify) {
                                $user = User::withTrashed()->where('id', $notify->id)->first();
                                if (Auth::id() != $user->id) {
                                    Notification::create([
                                        'user_id' => $user->id,
                                        'notification_message' =>  auth()->user()->name . ' moved from ' . $oldBoard . ' to ' . $newBoardName . ' in task: ' . $task->name . ' in project: ' . $projectFirst->project_title,
                                    ]);
                                }
                            }
                        }
                        else {
                            $task = Task::firstWhere('id', $task_id);

                            Report::create(['user_id' => $user_id, 'project_id' => $task->project_id, 'message' => ' updated a subtask in ' . $task->name . ' in project: ' . $projectFirst->project_title]);

                            // Notify all users in the task that a subtask is updated
                            foreach ($notifyAllSubtaskUser as $notify) {
                                $user = User::where('id', $notify->user_id)->first();
                                Notification::create([
                                    'user_id' => $user->id,
                                    'notification_message' =>  $user->name . ' updated a subtask in ' . $task->name . ' in project: ' . $projectFirst->project_title,
                                ]);
                            }
                        }
                        $totalSubtaskDone = Subtask::where('task_id', $task_id)->where('board_id', 2)
                            ->where('is_approved', 1)
                            ->get()->count();
                        TaskProgress::where('task_id', $task_id)
                            ->update(['total_subtask_done' => $totalSubtaskDone]);

                        $task = Subtask::join('tasks', 'tasks.id', '=', 'subtasks.task_id')
                            ->firstWhere('subtasks.task_id', $task_id);
                        $totalTaskDone = TaskProgress::where('board_id', $task->board_id)
                            ->whereColumn('total_subtask', 'total_subtask_done')
                            ->where('total_subtask', '>', 0)
                            ->where('total_subtask_done', '>', 0)
                            ->get()->count();
                        BoardProgress::where('board_id', $task->board_id)->update([
                            'total_task_done' => $totalTaskDone
                        ]);

                        return new SubtaskResource($subtask);
                    }
                }
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function subtaskApproval($id) {
        try{
            $subtask = Subtask::findOrFail($id);
            // if subtask not yet approved
            if($subtask->is_approved === 1) {
                $notifyUser = Subtask::where('task_id', $subtask->task_id)->where('user_id', $subtask->user_id)->first();
                $task = Task::firstWhere('id', $subtask->task_id);
                Subtask::firstWhere('id', $id)->update(['is_approved' => 0]);
                $countApproved = Subtask::where('task_id', $subtask->task_id)->where('board_id', 2)
                            ->where('is_approved', 1)
                            ->get()->count();
                TaskProgress::where('task_id', $subtask->task_id)
                    ->update(['total_subtask_done' => $countApproved]);
                $user = User::where('id', $notifyUser->user_id)->first();
                Notification::create([
                    'user_id' => $user->id,
                    'notification_message' =>  auth()->user()->name . ' disapproved your subtask:  ' . $subtask->subtask_name . ' in task: ' . $task->name,
                ]);
            }
            // if subtask is already approved
            else {
                $notifyUser = Subtask::where('task_id', $subtask->task_id)->where('user_id', $subtask->user_id)->first();
                $task = Task::firstWhere('id', $subtask->task_id);
                Subtask::firstWhere('id', $id)->update(['is_approved' => 1]);

                $countApproved = Subtask::where('task_id', $subtask->task_id)->where('board_id', 2)
                            ->where('is_approved', 1)
                            ->get()->count();
                TaskProgress::where('task_id', $subtask->task_id)
                    ->update(['total_subtask_done' => $countApproved]);
                $user = User::where('id', $notifyUser->user_id)->first();
                Notification::create([
                    'user_id' => $user->id,
                    'notification_message' =>  auth()->user()->name . ' approved your subtask:  ' . $subtask->subtask_name . ' in task: ' . $task->name,
                ]);
                Subtask::firstWhere('id', $id)->update(['is_approved' => 1]);
            }
        }
        catch(ModelNotFoundException $e){
            abort(404);
        }
        catch (Exception $e) {
            abort(500);
        }
    }
}
