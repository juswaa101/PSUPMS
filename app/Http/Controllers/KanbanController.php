<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;
use App\Models\Project;
use App\Models\Invitation;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KanbanController extends Controller
{
    public function index($id)
    {
        try {
            $project = Project::findOrFail($id);

            $fetch = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('project_id', '=', $id)
                ->get();

            $head = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->where('is_project_head', '=', 1)
                ->select('*')
                ->get();

            $staff = DB::table('projects')
                ->join('users', 'projects.id', '=', 'users.id')
                ->join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('projects.is_project_head', '=', 0)
                ->where('invitations.status', 1)
                ->select(['*', 'users.id as user_id'])
                ->get();

            $users = User::where('users.role', '!=', 'admin')->get();
            $user_head = User::join('projects', 'projects.id', '=', 'users.id')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->where('projects.is_project_head', '=', 1)
                ->first(['users.id as user_id', 'projects.*', 'users.*']);


            $userAssignedProject = DB::table('projects')
                ->join('users', 'projects.id', '=', 'users.id')
                ->where('project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->where('is_project_head', '=', 0)
                ->select('*')
                ->get();

            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();

            $projects = Project::limit(5)->orderByDesc('created_at')->get()->unique('project_title');
            $fetchLimitProject = Project::limit(5)->orderByDesc('created_at')->get()->unique('project_title');

            return view('admin.kanban', compact('project'))
                ->with('users', $users)
                ->with(compact('projects'))
                ->with(compact('notification'))
                ->with(compact('user_head'))
                ->with(compact('userAssignedProject'))
                ->with(compact('fetch'))
                ->with(compact('staff'))
                ->with(compact('head'))
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function projectHead($id)
    {
        try {
            $project = Project::findOrFail($id);

            $isProjectHead = Project::join('users', 'projects.id', '=', 'users.id')
                ->where('users.role', '!=', 'admin')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('projects.id', '=', Auth::user()->id)
                ->first(['is_project_head as is_project_head']);

            $fetch = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('project_id', '=', $id)
                ->get();

            $head = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->where('is_project_head', '=', 1)
                ->select('*')
                ->get();

            $staff = DB::table('projects')
                ->join('users', 'projects.id', '=', 'users.id')
                ->join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('projects.is_project_head', '=', 0)
                ->where('invitations.status', 1)
                ->select(['*', 'users.id as user_id'])
                ->get();

            $users = User::where('users.role', '!=', 'admin')->get();

            $user_head = User::join('projects', 'projects.id', '=', 'users.id')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->where('projects.is_project_head', '=', 1)
                ->first(['users.id as user_id', 'projects.*', 'users.*']);

            $userAssignedProject = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->where('is_project_head', '=', 0)
                ->select('*')
                ->get();

            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();

            $projects = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->limit(5)
                ->get();

            $invitation = Invitation::join('users', 'users.id', '=', 'invitations.user_id')
                ->join('projects', 'projects.project_id', '=', 'invitations.project_id')
                ->orderBy('invitations.created_at', 'desc')
                ->where('user_id', Auth::user()->id)
                ->select([
                    'invitations.id',
                    'invitations.status',
                    'invitations.invitation_message',
                    'invitations.created_at as created',
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

            return view('head.kanban', compact('project'))
                ->with('users', $users)
                ->with(compact('projects'))
                ->with(compact('notification'))
                ->with(compact('isProjectHead'))
                ->with(compact('user_head'))
                ->with(compact('userAssignedProject'))
                ->with(compact('fetch'))
                ->with(compact('staff'))
                ->with(compact('head'))
                ->with(compact('invitation'))
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function updateMembers(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'members' => 'required'
            ]);
            $all_members = $request->input('members');

            if ($validate->fails()) {
                // Return validation error
                return response()->json($validate->getMessageBag(), 400);
            } else {
                $project = Project::findOrFail($id);
                $fetchQuery = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                    ->join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                    ->where('project_title', '=', $project->project_title)
                    ->where('users.role', '!=', 'admin')
                    ->where('is_project_head', '=', 0)
                    ->where('invitations.status', 1)
                    ->get();
                $currentMembers = [];

                foreach ($fetchQuery as $row) {
                    $currentMembers[] = $row->id;
                }

                // Add new members
                foreach ($all_members as $new_members) {
                    if (!in_array($new_members, $currentMembers)) {
                        $project = Project::create([
                            'project_title' => $project->project_title,
                            'project_description' => $project->project_description,
                            'template' => $project->template,
                            'project_start_date' => $project->project_start_date,
                            'project_end_date' => $project->project_end_date,
                            'id' => $new_members,
                            'is_project_head' => 0,
                            'create_task_status' => $project->create_task_status,
                            'create_subtask_status' => $project->create_subtask_status
                        ]);

                        Report::create([
                            'user_id' => $new_members,
                            'message' => ' was assigned to the project: ' . $project->project_title,
                            'project_id' => $project->project_id
                        ]);

                        Notification::create([
                            'user_id' => $new_members,
                            'notification_message' => 'You were assigned to a project as project member in ' . $project->project_title,
                        ]);

                        Invitation::create([
                            'user_id' => $new_members,
                            'project_id' => $project->project_id,
                            'invitation_message' => Auth::user()->name . ' has invited you to a project: ' . $project->project_title,
                            'status' => 0,
                        ]);
                    }
                }

                // Remove members in project
                foreach ($currentMembers as $fetchedRow) {
                    if (!in_array($fetchedRow, $all_members)) {
                        Report::create([
                            'user_id' => $fetchedRow,
                            'message' => ' was removed to the project: ' . $project->project_title,
                            'project_id' => $project->project_id
                        ]);

                        Notification::create([
                            'user_id' => $fetchedRow,
                            'notification_message' => 'You were removed to a project as project member in ' . $project->project_title,
                        ]);

                        Invitation::join('projects', 'projects.project_id', '=', 'invitations.project_id')
                            ->where('user_id', $fetchedRow)
                            ->where('projects.project_title', $project->project_title)
                            ->delete();

                        Project::where('id', $fetchedRow)
                            ->where('project_title', $project->project_title)
                            ->delete();
                    }
                }
                return response()->json('Members updated successfully!');
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function destroy($title)
    {
        try {
            $project = Project::where('project_title', '=', $title);
            $project->delete();
            return response()->json(['message', 'Project deleted successfully!']);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
