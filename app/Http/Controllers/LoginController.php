<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Invitation;
use App\Models\Notification;
use App\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        try {
            return view('login');
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function authLogin(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            $user_data = array(
                'email' => $request->input('email'),
                'password' => $request->input('password')
            );

            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate)->withInput();
            } else {
                if (Auth::attempt($user_data)) {
                    if (Auth::user()->role == "admin") {
                        return redirect('/admin/dashboard');
                    } else {
                        return redirect('/head/dashboard/' . Auth::user()->uuid);
                    }
                } else {
                    return redirect()->back()
                        ->withInput()->with('error', 'Invalid email or password!');
                }
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function adminDashboard()
    {
        try {
            $fetch = Project::all()->where('id', Auth::user()->id)->sortByDesc('created_at');
            $research = Project::all()->where('id', Auth::user()->id)->sortByDesc('created_at')->where('template', 'research_extension')->unique('project_title');
            $igp = Project::all()->where('id', Auth::user()->id)->sortByDesc('created_at')->where('template', 'igp')->unique('project_title');
            $extension = Project::all()->where('id', Auth::user()->id)->sortByDesc('created_at')->where('template', 'extension_project')->unique('project_title');
            $default = Project::all()->where('id', Auth::user()->id)->where('template', 'default')->unique('project_title');
            $user_profile = User::where('id', Auth::user()->id)->get();
            $project = $fetch->unique('project_title');
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

            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();
            $finishedProject = Project::all()->where('id', Auth::user()->id)->sortByDesc('created_at')->where('is_finished', 1)->unique('project_title');
            $limitFinishedProject = Project::where('id', Auth::user()->id)->where('is_finished', 1)->limit(7)->get()->unique('project_title');
            return view('admin.dashboard', compact('project'), compact('notification'))
                ->with('user_profile', $user_profile)
                ->with('finishedProjects', $finishedProject)
                ->with('research', $research)
                ->with('igp', $igp)
                ->with('extension', $extension)
                ->with('default', $default)
                ->with('limitFinishedProject', $limitFinishedProject)
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function headDashboard($uuid)
    {
        try {
            $fetch = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->get();
            $research = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->where('projects.template', 'research_extension')
                ->orderBy('projects.created_at', 'desc')
                ->get();
            $igp = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->where('projects.template', 'igp')
                ->orderBy('projects.created_at', 'desc')
                ->get();
            $extension = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('projects.is_project_head', 1)
                ->orWhere('invitations.status', 1)
                ->where('projects.template', 'extension_project')
                ->orderBy('projects.created_at', 'desc')
                ->get();
            $default = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->where('projects.template', 'default')
                ->orderBy('projects.created_at', 'desc')
                ->get();
            $user_profile = User::where('id', $uuid)->get();
            $project = $fetch->unique('project_title');
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
                ->join('projects', 'projects.project_id', '=', 'tasks.project_id')
                ->whereNull('projects.deleted_at')
                ->where('task_members.user_id', Auth::user()->id)
                ->select(['tasks.*', 'task_members.*', 'task_members.id as tm_id', 'projects.project_title'])
                ->get();

            $taskDeadlineDate = array();
            foreach ($notifyTask as $taskDate) {
                if ($taskDate->task_due_date != null) {
                    $taskDeadlineDate[$taskDate->tm_id]['project_name'] = $taskDate->project_title;
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
                        'notification_message' => 'Task: ' . $value['task_name'] . ' in Project: ' . $value['project_name'] .  ' is approaching the deadline 2 days from now',
                        'has_read' => 0
                    ]);
                }
            }

            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();
            $finishedProject = Project::where('id', $uuid)->where('is_finished', 1)->limit(5)->get();
            $limitFinishedProject = Project::where('id', $uuid)->where('is_finished', 1)->limit(7)->get();
            $invitation = Invitation::join('users', 'users.id', '=', 'invitations.user_id')
                ->join('projects', 'projects.project_id', '=', 'invitations.project_id')
                ->orderBy('invitations.created_at', 'desc')
                ->where('user_id', Auth::user()->id)
                ->select([
                    'invitations.id',
                    'invitations.status',
                    'invitations.invitation_message',
                    'users.*',
                    'projects.project_id'
                ])
                ->get();

            return view('head.dashboard', compact('project'), compact('notification'))
                ->with('user_profile', $user_profile)
                ->with('finishedProjects', $finishedProject)
                ->with('invitation', $invitation)
                ->with('research', $research)
                ->with('igp', $igp)
                ->with('extension', $extension)
                ->with('default', $default)
                ->with('limitFinishedProject', $limitFinishedProject)
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
            return redirect('/');
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
