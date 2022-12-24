<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Project;
use App\Models\Invitation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportsController extends Controller
{
    public function adminReports()
    {
        try {
            $user_profile = User::where('id', Auth::user()->id)->get();
            $fetch = Project::where('id', Auth::user()->id)->limit(5)->orderByDesc('created_at')->get();
            $project = $fetch->unique('project_title');
            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();

            $fetchLimitProject = Project::where('id', Auth::user()->id)->limit(5)->orderByDesc('created_at')->get()->unique('project_title');

            return view('admin.reports.reports', compact('project'), compact('notification'))
                ->with('user_profile', $user_profile)
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function headReports()
    {
        try {
            $user_profile = User::where('id', Auth::user()->id)->get();
            $fetch = Project::join('invitations', 'invitations.project_id', '=', 'projects.project_id')
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->where('invitations.status', 1)
                ->limit(5)
                ->get();
            $project = $fetch->unique('project_title');
            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at', 'desc')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();
            $invitation = Invitation::join('users', 'users.id', '=', 'invitations.user_id')
                ->join('projects', 'projects.project_id', '=', 'invitations.project_id')
                ->orderBy('invitations.created_at')
                ->where('user_id', Auth::user()->id)
                ->select([
                    'invitations.id',
                    'invitations.invitation_message',
                    'invitations.status',
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
            return view('head.reports.reports', compact('project'), compact('notification'))
                ->with('user_profile', $user_profile)
                ->with('invitation', $invitation)
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function fetchProject()
    {
        try {
            $getProjects = Project::withTrashed()
                ->orderBy('project_title')
                ->get()
                ->unique('project_title');
            return response()->json(['projects' => $getProjects]);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function fetchHeadProject()
    {
        try {
            $getProjects = Project::withTrashed()
                ->orderByDesc('projects.created_at')
                ->where('projects.id', Auth::user()->id)
                ->get()
                ->unique('project_title');

            return response()->json(['projects' => $getProjects]);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function showReport($id, Request $request)
    {
        try {
            $project = Project::withTrashed()->firstWhere('project_id', $id);
            $projects = Project::withTrashed()->where('project_title', $project->project_title)->get();

            $get_projects_id = [];
            foreach ($projects as $proj) {
                $get_projects_id[] = $proj->project_id;
            }

            $report = $this->getCollection($request, $get_projects_id);
            return response()->json(['project' => $report]);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function getCollection(Request $request, array $get_projects_id)
    {
        try {
            if ($request->get('name') != null && $request->get('date') != null) {
                $report = DB::table('reports')
                    ->join('users', 'reports.user_id', '=', 'users.id')
                    ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                    ->whereIn('reports.project_id', $get_projects_id)
                    ->where('reports.user_id', $request->get('name'))
                    ->whereRaw('DATE(reports.created_at) = ?', [$request->get('date')])
                    ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                    ->orderBy('users.name')
                    ->get();
                return $report;
            } else if ($request->get('name') != null) {
                if ($request->get('quarter') != null) {
                    if ($request->get('quarter') == "quarter1") {
                        $start = Carbon::now()->month(1)->startOfQuarter();
                        $end = Carbon::now()->month(1)->endOfQuarter();
                        $report = DB::table('reports')
                            ->join('users', 'reports.user_id', '=', 'users.id')
                            ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                            ->whereIn('reports.project_id', $get_projects_id)
                            ->where('reports.user_id', $request->get('name'))
                            ->whereBetween('reports.created_at', [$start->format('Y/m/d'), $end->format('Y/m/d')])
                            ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                            ->orderBy('users.name')
                            ->get();
                        return $report;
                    } else if ($request->get('quarter') == "quarter2") {
                        $start = Carbon::now()->month(4)->startOfQuarter();
                        $end = Carbon::now()->month(4)->endOfQuarter();
                        $report = DB::table('reports')
                            ->join('users', 'reports.user_id', '=', 'users.id')
                            ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                            ->whereIn('reports.project_id', $get_projects_id)
                            ->where('reports.user_id', $request->get('name'))
                            ->whereBetween('reports.created_at', [$start->format('Y/m/d'), $end->format('Y/m/d')])
                            ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                            ->orderBy('users.name')
                            ->get();
                        return $report;
                    } else if ($request->get('quarter') == "quarter3") {
                        $start = Carbon::now()->month(7)->startOfQuarter();
                        $end = Carbon::now()->month(7)->endOfQuarter();
                        $report = DB::table('reports')
                            ->join('users', 'reports.user_id', '=', 'users.id')
                            ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                            ->whereIn('reports.project_id', $get_projects_id)
                            ->where('reports.user_id', $request->get('name'))
                            ->whereBetween('reports.created_at', [$start->format('Y/m/d'), $end->format('Y/m/d')])
                            ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                            ->orderBy('users.name')
                            ->get();
                        return $report;
                    } else {
                        $start = Carbon::now()->month(10)->startOfQuarter();
                        $end = Carbon::now()->month(10)->endOfQuarter();
                        $report = DB::table('reports')
                            ->join('users', 'reports.user_id', '=', 'users.id')
                            ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                            ->whereIn('reports.project_id', $get_projects_id)
                            ->where('reports.user_id', $request->get('name'))
                            ->whereBetween('reports.created_at', [$start->format('Y/m/d'), $end->format('Y/m/d')])
                            ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                            ->orderBy('users.name')
                            ->get();
                        return $report;
                    }
                } else {
                    $report = DB::table('reports')
                        ->join('users', 'reports.user_id', '=', 'users.id')
                        ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                        ->whereIn('reports.project_id', $get_projects_id)
                        ->where('reports.user_id', $request->get('name'))
                        ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                        ->orderBy('users.name')
                        ->get();
                    return $report;
                }
            } else if ($request->get('date') != null) {
                $report = DB::table('reports')
                    ->join('users', 'reports.user_id', '=', 'users.id')
                    ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                    ->whereIn('reports.project_id', $get_projects_id)
                    ->whereRaw('DATE(reports.created_at) = ?', [$request->get('date')])
                    ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                    ->orderBy('users.name')
                    ->get();
                return $report;
            } else if ($request->get('quarter') != null) {
                if ($request->get('quarter') == "quarter1") {
                    $start = Carbon::now()->month(1)->startOfQuarter();
                    $end = Carbon::now()->month(1)->endOfQuarter();
                    $report = DB::table('reports')
                        ->join('users', 'reports.user_id', '=', 'users.id')
                        ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                        ->whereIn('reports.project_id', $get_projects_id)
                        ->whereBetween('reports.created_at', [$start->format('Y/m/d'), $end->format('Y/m/d')])
                        ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                        ->orderBy('users.name')
                        ->get();
                    return $report;
                } else if ($request->get('quarter') == "quarter2") {
                    $start = Carbon::now()->month(4)->startOfQuarter();
                    $end = Carbon::now()->month(4)->endOfQuarter();
                    $report = DB::table('reports')
                        ->join('users', 'reports.user_id', '=', 'users.id')
                        ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                        ->whereIn('reports.project_id', $get_projects_id)
                        ->whereBetween('reports.created_at', [$start->format('Y/m/d'), $end->format('Y/m/d')])
                        ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                        ->orderBy('users.name')
                        ->get();
                    return $report;
                } else if ($request->get('quarter') == "quarter3") {
                    $start = Carbon::now()->month(7)->startOfQuarter();
                    $end = Carbon::now()->month(7)->endOfQuarter();
                    $report = DB::table('reports')
                        ->join('users', 'reports.user_id', '=', 'users.id')
                        ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                        ->whereIn('reports.project_id', $get_projects_id)
                        ->whereBetween('reports.created_at', [$start->format('Y/m/d'), $end->format('Y/m/d')])
                        ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                        ->orderBy('users.name')
                        ->get();
                    return $report;
                } else {
                    $start = Carbon::now()->month(10)->startOfQuarter();
                    $end = Carbon::now()->month(10)->endOfQuarter();
                    $report = DB::table('reports')
                        ->join('users', 'reports.user_id', '=', 'users.id')
                        ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                        ->whereIn('reports.project_id', $get_projects_id)
                        ->whereBetween('reports.created_at', [$start->format('Y/m/d'), $end->format('Y/m/d')])
                        ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                        ->orderBy('users.name')
                        ->get();
                    return $report;
                }
            } else {
                $report = DB::table('reports')
                    ->join('users', 'reports.user_id', '=', 'users.id')
                    ->join('projects', 'reports.project_id', '=', 'projects.project_id')
                    ->whereIn('reports.project_id', $get_projects_id)
                    ->select(['reports.id as report_id', 'reports.created_at as report_date', 'reports.message', 'reports.project_id', 'users.*', 'projects.*'])
                    ->orderBy('users.name')
                    ->get();
                return $report;
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function showReportHead($id, Request $request)
    {
        try {
            $project = Project::withTrashed()->firstWhere('project_id', $id);
            $projects = Project::withTrashed()->where('project_title', $project->project_title)->get();

            $get_projects_id = [];
            foreach ($projects as $proj) {
                $get_projects_id[] = $proj->project_id;
            }

            $report = $this->getCollection($request, $get_projects_id);
            return response()->json(['project' => $report]);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function exportPdfAdmin($id, Request $request)
    {
        try {
            return $this->filterProject($id, $request);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function filterProject($id, Request $request): BinaryFileResponse
    {
        try {
            $project = Project::withTrashed()->firstWhere('project_id', $id);
            $projects = Project::withTrashed()->orderByDesc('projects.created_at')
                ->where('projects.project_title', $project->project_title)
                ->get();

            $get_projects_id = [];
            foreach ($projects as $proj) {
                $get_projects_id[] = $proj->project_id;
            }

            $reports = $this->getCollection($request, $get_projects_id);

            $path = public_path() . '/assets/login/psu.png';
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $image = 'data:image/' . $type . ';base64,' . base64_encode($data);

            $pdf = Pdf::setOptions(['isRemoteEnabled' => FALSE])
                ->loadView('pdf.reports', ['reports' => $reports, 'image' => $image]);

            $path = public_path('pdf/');
            $fileName = time() . '.' . 'pdf';
            $pdf->save($path . '/' . $fileName);
            $pdf = public_path('pdf/' . $fileName);

            return response()->download($pdf);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function exportPdfHead($id, Request $request)
    {
        try {
            return $this->filterProject($id, $request);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function showEmployees($id)
    {
        try {
            $project = Project::withTrashed()->findOrFail($id);
            $users = Project::withTrashed()->join('users', 'projects.id', '=', 'users.id')
                ->where('projects.project_title', '=', $project->project_title)
                ->whereNot('users.role', '=', 'admin')
                ->orderBy('users.name')
                ->get();
            return response()->json(['users' => $users]);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
