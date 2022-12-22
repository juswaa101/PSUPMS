<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Invitation;
use App\Models\Notification;
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
                        return redirect('/head/dashboard/' . Auth::user()->id);
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
            $fetch = Project::all()->sortByDesc('created_at');
            $research = Project::all()->sortByDesc('created_at')->where('template', 'research_extension')->unique('project_title');
            $igp = Project::all()->sortByDesc('created_at')->where('template', 'igp')->unique('project_title');
            $extension = Project::all()->sortByDesc('created_at')->where('template', 'extension_project')->unique('project_title');
            $default = Project::all()->where('template', 'default')->unique('project_title');
            $user_profile = User::where('id', Auth::user()->id)->get();
            $project = $fetch->unique('project_title');
            $fetchLimitProject = Project::limit(5)->orderByDesc('created_at')->get()->unique('project_title');
            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();
            $finishedProject = Project::all()->sortByDesc('created_at')->where('is_finished', 1)->unique('project_title');
            $limitFinishedProject = Project::where('is_finished', 1)->limit(7)->get()->unique('project_title');
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

    public function headDashboard($id)
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
            $user_profile = User::where('id', $id)->get();
            $project = $fetch->unique('project_title');
            $fetchLimitProject = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->limit(5)
                ->get();
            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();
            $finishedProject = Project::where('id', $id)->where('is_finished', 1)->limit(5)->get();
            $limitFinishedProject = Project::where('id', $id)->where('is_finished', 1)->limit(7)->get();
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
