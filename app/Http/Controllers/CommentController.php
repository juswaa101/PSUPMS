<?php

namespace App\Http\Controllers;

use App\Task;
use Exception;
use App\Models\User;
use App\Models\Report;
use App\Models\Comment;
use App\Models\Project;
use App\Models\TaskMember;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function show($id)
    {
        try {
            $comments = DB::table('comments')
                ->join('users', 'comments.user_id', '=', 'users.id')
                ->where('comments.task_id', $id)
                ->select(
                    'users.image',
                    'users.name',
                    'comments.created_at as date_created',
                    'comments.user_id',
                    'comments.comment',
                    'comments.id'
                )
                ->orderByDesc('comments.created_at')
                ->get();

            return response()->json($comments);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'comment' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                Comment::create([
                    'task_id' => $request->input('task_id'),
                    'user_id' => auth()->user()->id,
                    'comment' => $request->input('comment')
                ]);

                $id = TaskMember::where('task_id',  $request->input('task_id'))->get();
                $task = Task::firstWhere('id', $request->input('task_id'));
                $project = Project::firstWhere('project_id', $task->project_id);
                Report::create(['user_id' => auth()->user()->id, 'project_id' => $task->project_id, 'message' => ' commented in a task: ' . $task->name]);

                foreach ($id as $notify) {
                    $user = User::where('id', $notify->user_id)->first();
                    Notification::create([
                        'user_id' => $user->id,
                        'notification_message' => Auth::user()->name . ' commented on task: ' . $task->name . ' in ' . $project->project_title,
                    ]);
                }

                return response()->json('Comment added');
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $task = Task::firstWhere('id', $comment->task_id);
            $project = Project::firstWhere('project_id', $task->project_id);

            Report::create(['user_id' => auth()->user()->id, 'project_id' => $task->project_id, 'message' => ' deleted a comment in a task: ' . $task->name]);

            $task_member = TaskMember::where('task_id', $comment->task_id)->get();

            foreach ($task_member as $comment) {
                Notification::create([
                    'user_id' => $comment->user_id,
                    'notification_message' => Auth::user()->name . ' deleted a comment in task: ' . $task->name . ' in ' . $project->project_title,
                ]);
            }

            return response()->json(Comment::findOrFail($id)->delete());
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'comment' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                Comment::findOrFail($id)->update([
                    'task_id' => $request->input('task_id'),
                    'user_id' => auth()->user()->id,
                    'comment' => $request->input('comment')
                ]);

                $id = TaskMember::where('task_id',  $request->input('task_id'))->get();
                $task = Task::firstWhere('id', $request->input('task_id'));
                $project = Project::firstWhere('project_id', $task->project_id);

                Report::create(['user_id' => auth()->user()->id, 'project_id' => $task->project_id, 'message' => ' updated a comment in a task: ' . $task->name . ' in ' . $project->project_title]);

                foreach ($id as $notify) {
                    $task_user = User::where('id', $notify->user_id)->first();
                    Notification::create([
                        'user_id' => $task->user_id,
                        'notification_message' => Auth::user()->name . ' updated a comment in ' . $task->name,
                    ]);
                }
                return response()->json('Comment updated!');
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
