<?php

namespace App\Http\Controllers;

use App;
use App\Task;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use App\Models\Project;
use App\Models\Invitation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ProjectUpdateRequest;

class ProjectController extends Controller
{
    public function index()
    {
        try {
            $members = User::where('role', '=', 'teaching')
                ->orWhere('role', '=', 'non-teaching')
                ->where('id', '!=', Auth::user()->id)
                ->orderBy('name')->get();
            $user_profile = User::where('id', Auth::user()->id)->get();
            $fetch = Project::orderByDesc('created_at')->where('id', Auth::user()->id)->limit(5)->get();
            $project = $fetch->unique('project_title');
            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();

            $fetchLimitProject = Project::where('id', Auth::user()->id)->limit(5)->orderByDesc('created_at')->get()->unique('project_title');
            // notify the admin
            $notifyProject = Project::all()->unique('project_title');
            $deadlineDate = array();
            foreach ($notifyProject as $remindDate) {
                $deadlineDate[$remindDate->project_id]['title'] = $remindDate->project_title;
                $deadlineDate[$remindDate->project_id]['date'] = $remindDate->project_end_date;
            }
            foreach ($deadlineDate as $deadDate => $value) {
                $current = Carbon::now();
                $due_date = Carbon::parse($value['date']);
                if ($current->diffInDays($due_date, false) == 2) {
                    Notification::create([
                        'user_id' => Auth::user()->id,
                        'notification_message' => 'Project: ' . $value['title'] . ' is approaching the deadline 2 days from now',
                        'has_read' => 0
                    ]);
                }
            }

            return view('admin.project', ['members' => $members, 'notification' => $notification], compact('project'))
                ->with('user_profile', $user_profile)
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function head()
    {
        try {
            $members = User::where('role', '=', 'teaching')
                ->orWhere('role', '=', 'non-teaching')
                ->where('id', '!=', Auth::user()->id)
                ->orderBy('name')->get();
            $fetch = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->limit(5)
                ->get();

            $user_profile = User::where('id', Auth::user()->id)->get();
            $project = $fetch->unique('project_title');
            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();
            $invitation = Invitation::join('users', 'users.id', '=', 'invitations.user_id')
                ->join('projects', 'projects.project_id', '=', 'invitations.project_id')
                ->orderBy('invitations.created_at')
                ->where('user_id', Auth::user()->id)
                ->select([
                    'invitations.id',
                    'invitations.status',
                    'invitations.invitation_message',
                    'users.*',
                    'projects.project_id'
                ])
                ->get();
            $fetchLimitProject = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->limit(5)
                ->get();
            // notify user in the project about the project deadline
            $notifyProject = Project::join('invitations', 'invitations.project_id', 'projects.project_id')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->get()
                ->unique('project_title');

            $deadlineDate = array();
            foreach ($notifyProject as $remindDate) {
                $deadlineDate[$remindDate->project_id]['title'] = $remindDate->project_title;
                $deadlineDate[$remindDate->project_id]['date'] = $remindDate->project_end_date;
            }

            foreach ($deadlineDate as $deadDate => $value) {
                $current = Carbon::now();
                $due_date = Carbon::parse($value['date']);
                if ($current->diffInDays($due_date, false) == 2) {
                    Notification::create([
                        'user_id' => Auth::user()->id,
                        'notification_message' => 'Project: ' . $value['title'] . ' is approaching the deadline 2 days from now',
                        'has_read' => 0
                    ]);
                }
            }

            // notify the user about the task deadline
            $notifyTask = Task::join('task_members', 'task_members.task_id', '=', 'tasks.id')
                ->where('task_members.user_id', Auth::user()->id)
                ->select(['tasks.*', 'task_members.*', 'task_members.id as tm_id'])
                ->get();

            $taskDeadlineDate = array();
            foreach ($notifyTask as $taskDate) {
                if ($taskDate->task_due_date != null) {
                    $taskDeadlineDate[$taskDate->tm_id]['task_name'] = $taskDate->name;
                    $taskDeadlineDate[$taskDate->tm_id]['task_due_date'] = $taskDate->task_due_date;
                }
            }
            
            foreach ($taskDeadlineDate as $taskDate => $value) {
                $current = Carbon::now();
                $due_date = Carbon::parse($value['task_due_date']);

                if ($current->diffInDays($due_date, false) == 2) {
                    Notification::create([
                        'user_id' => Auth::user()->id,
                        'notification_message' => 'Task: ' . $value['task_name'] . ' is approaching the deadline 2 days from now',
                        'has_read' => 0
                    ]);
                }
            }

            return view('head.project', ['members' => $members, 'notification' => $notification])->with(compact('project'))
                ->with('user_profile', $user_profile)
                ->with('invitation', $invitation)
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function getHead()
    {
        try {
            $heads = DB::table('users')->where('role', '=', 'teaching')
                ->orWhere('role', '=', 'non-teaching')
                ->orderBy('name')
                ->get();
            return response()->json($heads);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'title' => 'required|unique:projects,project_title|max:255',
                'description' => 'required',
                'project_start_date' => 'required',
                'project_end_date' => 'required|after:project_start_date',
                'head' => 'required',
                'staff' => 'required',
                'template' => 'required'
            ]);

            $staff = $request->input('staff');
            $head = $request->input('head');
            $checkToggleTask = (int)$request->has('toggle-task') ? 1 : 0;
            $checkToggleSubtask = (int)$request->has('toggle-subtask') ? 1 : 0;

            if ($validate->fails()) {
                return back()->withErrors($validate)->withInput();
            } else {
                $project = Project::create([
                    'project_title' => $request->input('title'),
                    'project_description' => $request->input('description'),
                    'project_start_date' => $request->input('project_start_date'),
                    'project_end_date' => $request->input('project_end_date'),
                    'id' => Auth::user()->id,
                    'is_project_head' => 1,
                    'create_task_status' => $checkToggleTask,
                    'create_subtask_status' => $checkToggleSubtask,
                    'template' => $request->input('template')
                ]);
                Report::create(['user_id' => Auth::user()->id, 'project_id' => $project->project_id, 'message' => ' has created the project: ' . $request->input('title') . ' as a project head']);

                foreach ($staff as $st) {
                    $project = Project::create([
                        'project_title' => $request->input('title'),
                        'project_description' => $request->input('description'),
                        'project_start_date' => $request->input('project_start_date'),
                        'project_end_date' => $request->input('project_end_date'),
                        'id' => $st,
                        'is_project_head' => 0,
                        'create_task_status' => $checkToggleTask,
                        'create_subtask_status' => $checkToggleSubtask,
                        'template' => $request->input('template')
                    ]);

                    $id = $project->project_id;
                    Report::create(['user_id' => $st, 'project_id' => $id, 'message' => ' was assigned by ' . Auth::user()->name . ' to a project: ' . $request->input('title') . ' as a project member']);

                    $user = User::where('id', $st)->first();
                    Notification::create([
                        'user_id' => $user->id,
                        'notification_message' => 'You were assigned to a project as project member in ' . $request->input('title') . ' by admin',
                    ]);

                    Invitation::create([
                        'user_id' => $st,
                        'project_id' => $id,
                        'invitation_message' => Auth::user()->name . ' has invited you to a project: ' . $request->title,
                        'status' => 0,
                    ]);
                }

                foreach ($head as $h) {
                    $project = Project::create([
                        'project_title' => $request->input('title'),
                        'project_description' => $request->input('description'),
                        'project_start_date' => $request->input('project_start_date'),
                        'project_end_date' => $request->input('project_end_date'),
                        'id' => $h,
                        'is_project_head' => 1,
                        'create_task_status' => $checkToggleTask,
                        'create_subtask_status' => $checkToggleSubtask,
                        'template' => $request->input('template')
                    ]);

                    $id = $project->project_id;
                    Report::create([
                        'user_id' => $h, 'project_id' => $id, 'message' => ' was assigned by ' .
                            Auth::user()->name . ' to a project: ' . $request->input('title')
                            . ' as a project head'
                    ]);

                    Invitation::create([
                        'user_id' => $h,
                        'project_id' => $id,
                        'invitation_message' => 'You were automatically assigned as project head in ' . $request->title,
                        'status' => 1,
                    ]);

                    $user = User::where('id', $h)->first();
                    Notification::create([
                        'user_id' => $user->id,
                        'notification_message' => 'You were assigned to a project as project head in ' . $request->input('title') . ' by admin',
                    ]);
                }

                return back()->with('success', 'Project Created Successfully!');
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function update(ProjectUpdateRequest $request, $id)
    {
        try {
            $validate = $request->validated();
            if (!$validate) {
                return response()->json($validate->getMessageBag(), 422);
            } else {
                $query = Project::findOrFail($id);
                $project_notify = Project::where('project_title', $query->project_title)->get();

                Report::create(['user_id' => auth()->user()->id, 'project_id' => $query->project_id, 'message' => ' updated the project details']);

                foreach ($project_notify as $notify) {
                    $user = User::withTrashed()->where('id', $notify->id)->first();
                    Notification::create([
                        'user_id' => $user->id,
                        'notification_message' => Auth::user()->name . ' has updated the project details in ' . $query->project_title,
                    ]);
                }

                Project::where('project_title', $query->project_title)->update($request->all());

                return response()->json([
                    'success', 200
                ]);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function storeHeadProject(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'title' => 'required|unique:projects,project_title|max:255',
                'description' => 'required',
                'project_start_date' => 'required',
                'project_end_date' => 'required|after:project_start_date',
                'staff' => 'required',
                'template' => 'required'
            ]);

            $staff = $request->input('staff');
            $checkToggleTask = (int)$request->has('toggle-task') ? 1 : 0;
            $checkToggleSubtask = (int)$request->has('toggle-subtask') ? 1 : 0;

            if ($validate->fails()) {
                return back()->withErrors($validate)->withInput();
            } else {
                Project::create([
                    'project_title' => $request->input('title'),
                    'project_description' => $request->input('description'),
                    'project_start_date' => $request->input('project_start_date'),
                    'project_end_date' => $request->input('project_end_date'),
                    'id' => 1,
                    'is_project_head' => 1,
                    'create_task_status' => $checkToggleTask,
                    'create_subtask_status' => $checkToggleSubtask,
                    'template' => $request->input('template')
                ]);

                $project = Project::create([
                    'project_title' => $request->input('title'),
                    'project_description' => $request->input('description'),
                    'project_start_date' => $request->input('project_start_date'),
                    'project_end_date' => $request->input('project_end_date'),
                    'id' => Auth::user()->id,
                    'is_project_head' => 1,
                    'create_task_status' => $checkToggleTask,
                    'create_subtask_status' => $checkToggleSubtask,
                    'template' => $request->input('template')
                ]);

                Invitation::create([
                    'user_id' => Auth::user()->id,
                    'project_id' => $project->project_id,
                    'invitation_message' => 'You were automatically assigned as project head in ' . $request->title,
                    'status' => 1,
                ]);

                $user = User::where('id', 1)->first();
                Notification::create([
                    'user_id' => $user->id,
                    'notification_message' => 'You were assigned to a project as admin/project head in ' . $request->input('title') . ' by ' . Auth::user()->name . ' - Project Head',
                ]);

                foreach ($staff as $st) {
                    $project = Project::create([
                        'project_title' => $request->input('title'),
                        'project_description' => $request->input('description'),
                        'project_start_date' => $request->input('project_start_date'),
                        'project_end_date' => $request->input('project_end_date'),
                        'id' => $st,
                        'is_project_head' => 0,
                        'create_task_status' => $checkToggleTask,
                        'create_subtask_status' => $checkToggleSubtask,
                        'template' => $request->input('template')
                    ]);

                    $id = $project->project_id;

                    Report::create([
                        'user_id' => $st, 'project_id' => $id, 'message' => ' was assigned by ' . Auth::user()->name . ' to a project: '
                            . $request->input('title') . ' as a project member'
                    ]);

                    $user = User::where('id', $st)->first();
                    Notification::create([
                        'user_id' => $user->id,
                        'notification_message' => 'You were assigned to a project as project member in ' . $request->input('title') . ' by ' . Auth::user()->name . ' - Project Head',
                    ]);

                    Invitation::create([
                        'user_id' => $st,
                        'project_id' => $id,
                        'invitation_message' => Auth::user()->name . ' has invited you to a project: ' . $request->title,
                        'status' => 0,
                    ]);
                }
                return back()->with('success', 'Project Created Successfully!');
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function toggleFinishedProject($id)
    {
        try {
            $project = Project::findOrFail($id);
            Project::where('id', Auth::user()->id)
                ->where('project_title', $project->project_title)
                ->update(['is_finished' => 1]);
            return response()->json(['msg' => 'success'], 200);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function toggleUnfinishedProject($id)
    {
        try {
            $project = Project::findOrFail($id);
            Project::where('id', Auth::user()->id)
                ->where('project_title', $project->project_title)
                ->update(['is_finished' => 0]);
            return redirect()->back();
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function getAllDueDate()
    {
        try {
            if (Auth::user()->role == 'admin') {
                $validateDate = Project::all()->unique("project_title");
                $dateProject = [];
                foreach ($validateDate as $date) {
                    $dateProject[] = $date->project_end_date;
                }
                return response()->json(['due_date' => $dateProject]);
            } else {
                $validateDate = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                    ->where('projects.id', Auth::user()->id)
                    ->where('invitations.status', 1)
                    ->get();
                $dateProject = [];
                foreach ($validateDate as $date) {
                    $dateProject[] = $date->project_end_date;
                }
                return response()->json(['due_date' => $dateProject]);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
