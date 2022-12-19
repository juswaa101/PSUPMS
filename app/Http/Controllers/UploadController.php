<?php

namespace App\Http\Controllers;

use App\Models\AttachedFile;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Report;
use App\Models\TaskMember;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function show($task_id)
    {
        try {
            return response()->json(AttachedFile::where('task_id', $task_id)->get());
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'upload_file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,doc,pptx,xls,xlsx,txt|max:50000'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->getMessageBag(), 400);
            } else {
                if ($request->hasFile('upload_file')) {

                    $fileName = time() . '-' . $request->file('upload_file')->getClientOriginalName();
                    $request->file('upload_file')->move('assets/attached_files', $fileName);

                    // Store file also in database
                    AttachedFile::create([
                        'user_id' => auth()->user()->id,
                        'task_id' => $request->input('task_id'),
                        'filepath' => $fileName
                    ]);

                    $task = Task::firstWhere('id', $request->input('task_id'));
                    $project = Project::firstWhere('project_id', $task->project_id);

                    Report::create(['user_id' => auth()->user()->id, 'project_id' => $task->project_id, 'message' => ' upload a file in a task: ' . $task->name]);

                    // Notify users
                    $user_id = TaskMember::where('task_id', $request->input('task_id'))->get();
                    foreach ($user_id as $notify) {
                        $user = User::where('id', $notify->user_id)->first();
                        Notification::create([
                            'user_id' => $user->id,
                            'notification_message' => Auth::user()->name . ' uploaded a file in ' . $task->name . ' in ' . $project->project_title,
                        ]);
                    }
                }
            }
            return response()->json('File Uploaded');
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function download($file)
    {
        try {
            return response()->download(public_path('assets/attached_files/' . $file));
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function destroy($id, $file)
    {
        try {
            if (File::exists(public_path('assets/attached_files/' . $file))) {
                File::delete(public_path('assets/attached_files/' . $file));
                $attachedFile = AttachedFile::firstWhere('id', $id);
                $task = Task::firstWhere('id', $attachedFile->task_id);
                $project = Project::firstWhere('project_id', $task->project_id);
                $task_member = TaskMember::where('task_id', $attachedFile->task_id)->get();

                foreach ($task_member as $member) {
                    Notification::create([
                        'user_id' => $member->user_id,
                        'notification_message' => Auth::user()->name . ' deleted a file in ' . $task->name . ' in ' . $project->project_title,
                    ]);
                }

                AttachedFile::where('id', $id)->delete();
                Report::create(['user_id' => auth()->user()->id, 'project_id' => $task->project_id, 'message' => ' deleted a file in a task: ' . $task->name]);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
