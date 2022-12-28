<?php

namespace App\Http\Controllers;

use App\Task;
use App\Board;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use App\Models\Project;
use App\Models\Invitation;
use App\Models\TaskMember;
use App\Models\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KanbanController extends Controller
{
    public function index($uuid)
    {
        try {
            $project = Project::where('uuid', $uuid)->firstOrFail();
            $findProject = Project::all()->where('project_title', $project->project_title);

            $fetch = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('projects.uuid', '=', $uuid)
                ->get();

            $head = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->whereNull('users.deleted_at')
                ->where('is_project_head', '=', 1)
                ->select('*')
                ->get();

            $staff = DB::table('projects')
                ->join('users', 'projects.id', '=', 'users.id')
                ->join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('projects.is_project_head', '=', 0)
                ->where('invitations.status', 1)
                ->whereNull('users.deleted_at')
                ->select(['*', 'users.id as user_id'])
                ->get();

            $users = User::where('users.role', '!=', 'admin')->get();
            $user_head = User::join('projects', 'projects.id', '=', 'users.id')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->whereNull('users.deleted_at')
                ->where('projects.is_project_head', '=', 1)
                ->first(['users.id as user_id', 'projects.*', 'users.*']);


            $userAssignedProject = DB::table('projects')
                ->join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->join('users', 'projects.id', '=', 'users.id')
                ->where('project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->whereNull('users.deleted_at')
                ->where('projects.is_project_head', '=', 0)
                ->where('invitations.status', 1)
                ->select('*')
                ->get();

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

            $projects = Project::where('id', Auth::user()->id)->limit(5)->orderByDesc('created_at')->get()->unique('project_title');
            $fetchLimitProject = Project::where('id', Auth::user()->id)->limit(5)->orderByDesc('created_at')->get()->unique('project_title');

            $project_id = [];
            foreach ($findProject as $item) {
                $project_id[] = $item->project_id;
            }
            $kanbanTask = Task::join('boards', 'boards.id', '=', 'tasks.board_id')
                ->whereIn('tasks.project_id', $project_id)
                ->select('boards.*', 'tasks.*', 'boards.name as board_name', 'tasks.name as task_name')
                ->get();
            $kanbanBoardAndTask = Board::orderBy('index')->whereIn('project_id', $project_id)->get();

            return view('admin.kanban', compact('project'))
                ->with('users', $users)
                ->with(compact('projects'))
                ->with(compact('notification'))
                ->with(compact('user_head'))
                ->with(compact('userAssignedProject'))
                ->with(compact('fetch'))
                ->with(compact('staff'))
                ->with(compact('head'))
                ->with(compact('kanbanTask'))
                ->with(compact('kanbanBoardAndTask'))
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function projectHead($uuid)
    {
        try {
            $project = Project::where('uuid', $uuid)->firstOrFail();
            $findProject = Project::all()->where('project_title', $project->project_title);

            $isProjectHead = Project::join('users', 'projects.id', '=', 'users.id')
                ->where('users.role', '!=', 'admin')
                ->whereNull('users.deleted_at')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('projects.id', '=', Auth::user()->id)
                ->first(['is_project_head as is_project_head']);

            $fetch = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('projects.uuid', '=', $uuid)
                ->get();

            $head = DB::table('projects')->join('users', 'projects.id', '=', 'users.id')
                ->where('project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->where('is_project_head', '=', 1)
                ->whereNull('users.deleted_at')
                ->select('*')
                ->get();

            $staff = DB::table('projects')
                ->join('users', 'projects.id', '=', 'users.id')
                ->join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('projects.is_project_head', '=', 0)
                ->where('invitations.status', 1)
                ->whereNull('users.deleted_at')
                ->select(['*', 'users.id as user_id'])
                ->get();

            $users = User::where('users.role', '!=', 'admin')->get();

            $user_head = User::join('projects', 'projects.id', '=', 'users.id')
                ->where('projects.project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->where('projects.is_project_head', '=', 1)
                ->whereNull('users.deleted_at')
                ->first(['users.id as user_id', 'projects.*', 'users.*']);

            $userAssignedProject = DB::table('projects')
                ->join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->join('users', 'projects.id', '=', 'users.id')
                ->where('project_title', '=', $project->project_title)
                ->where('users.role', '!=', 'admin')
                ->whereNull('users.deleted_at')
                ->where('projects.is_project_head', '=', 0)
                ->where('invitations.status', 1)
                ->select('*')
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

            $project_id = [];
            foreach ($findProject as $item) {
                $project_id[] = $item->project_id;
            }
            $kanbanTask = Task::join('boards', 'boards.id', '=', 'tasks.board_id')
                ->whereIn('tasks.project_id', $project_id)
                ->select('boards.*', 'tasks.*', 'boards.name as board_name', 'tasks.name as task_name')
                ->get();

            $kanbanBoardAndTask = Board::orderBy('index')->whereIn('project_id', $project_id)->get();
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
                ->with(compact('kanbanTask'))
                ->with(compact('kanbanBoardAndTask'))
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (ModelNotFoundException $e) {
            abort(404);
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
                    ->select(['projects.*', 'projects.id as userId', 'invitations.user_id as inv_user_id',  'invitations.*', 'users.*', 'users.id as uid'])
                    ->get();

                $currentMembers = [];

                foreach ($fetchQuery as $row) {
                    $currentMembers[] = $row->userId;
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
                            ->forceDelete();

                        TaskMember::join('projects', 'projects.uuid', '=', 'task_members.project_uuid')
                            ->where('task_members.user_id', $fetchedRow)
                            ->where('projects.project_title', $project->project_title)
                            ->delete();
                    }
                }
                return response()->json('Members updated successfully!');
            }
        } catch (ModelNotFoundException $e) {
            abort(404);
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
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function isInvited()
    {
        try {
            $url = $this->prev_segments(url()->previous());
            $uuid = null;
            for ($i = 0; $i < count($url); $i++) {
                if ($i == count($url) - 1) {
                    $uuid = $url[$i];
                }
            }
            $project = Project::firstWhere('uuid', $uuid);
            $query = Invitation::join('projects', 'projects.project_id', '=', 'invitations.project_id')
                ->where('projects.project_title', $project->project_title)
                ->where('projects.is_project_head', 0)
                ->where('invitations.status', 0)
                ->select(['projects.*', 'projects.id as pid', 'invitations.user_id as inv_user_id', 'invitations.*'])
                ->get();

            $idQuery = [];

            foreach ($query as $q) {
                $idQuery[] = $q->inv_user_id;
            }
            return response()->json(['invitation' => $idQuery]);
        } 
        catch (ModelNotFoundException $e) {
            abort(404);
        } 
        catch (Exception $e) {
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
