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
use App\Models\TaskProgress;
use Illuminate\Support\Facades\Validator;

class SubtaskController extends Controller
{
    public function index($task_id, $user_id)
    {
        try {
            // Fetch subtask per each user that is assigned in the task
            $subtask = Subtask::where('user_id', $user_id)
                ->where('task_id', $task_id)->get();

            return SubtaskResource::collection($subtask);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function show($id, $task_id, $user_id)
    {
        try {
            // Fetch subtask per each user that is assigned in the task
            $subtask = Subtask::where('user_id', $user_id)
                ->where('task_id', $task_id)
                ->findOrFail($id);
            
            // return subtask as a resource
            return new SubtaskResource($subtask);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'subtask_name' => 'required|max:255',
                'subtask_description' => 'required',
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

                $task = Task::firstWhere('id', $request->input('task_id'));
                $project = Project::firstWhere('project_id', $task->project_id);

                $totalSubtask = Subtask::where('task_id', $request->input('task_id'))->get()->count();
                TaskProgress::where('task_id', $request->input('task_id'))
                    ->update(['total_subtask' => $totalSubtask]);


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
                    'user_id' => $user->id,
                    'notification_message' =>  Auth::user()->name . ' deleted a subtask: ' . $subtask->subtask_name . ' in ' . $task->name . ' in ' . $project->project_title,
                ]);
            }

            Report::create(['user_id' => auth()->user()->id, 'project_id' => $task->project_id, 'message' => ' deleted a subtask in ' . $task->name]);

            $subtask->delete();

            $totalSubtask = Subtask::where('task_id', $task_id)->get()->count();
            TaskProgress::where('task_id', $task_id)
                ->update(['total_subtask' => $totalSubtask]);

            return new SubtaskResource($subtask);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    // drag and drop, on drop update na dapat
    public function update(Request $request, $id, $task_id, $user_id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'subtask_name' => 'required|max:255',
                'subtask_description' => 'required|max:255',
            ]);

            // Check Validations for Update
            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                $subtask = Subtask::where('user_id', $user_id)
                    ->where('task_id', $task_id)
                    ->findOrFail($id);

                $notifyAllSubtaskUser = Subtask::where('task_id', $task_id)->get();

                // Update Subtask
                $subtask->board_id = $request->input('board_id');
                $subtask->subtask_name = $request->input('subtask_name');
                $subtask->subtask_description = $request->input('subtask_description');


                if ($subtask->save()) {
                    $totalSubtask = Subtask::where('task_id', $task_id)->where('board_id', 2)
                        ->get()->count();
                    TaskProgress::where('task_id', $task_id)
                        ->update(['total_subtask_done' => $totalSubtask]);

                    $task = Task::firstWhere('id', $task_id);

                    Report::create(['user_id' => $user_id, 'project_id' => $task->project_id, 'message' => ' updated a subtask in ' . $task->name]);

                    // Notify all users in the task that a subtask is updated
                    foreach ($notifyAllSubtaskUser as $notify) {
                        $user = User::where('id', $notify->user_id)->first();
                        Notification::create([
                            'user_id' => $user->id,
                            'notification_message' =>  $user->name . ' updated a subtask in ' . $task->name,
                        ]);
                    }

                    return new SubtaskResource($subtask);
                }
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
