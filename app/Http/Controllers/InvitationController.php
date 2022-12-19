<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Project;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class InvitationController extends Controller
{
    public function countInvitation()
    {
        try {
            $invitation = Invitation::join('users', 'users.id', '=', 'invitations.user_id')
                ->where('invitations.user_id', Auth::user()->id)
                ->where('invitations.status', 0)->get();
            return response()->json([
                'invitation' => $invitation,
                'status' => 200
            ]);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function acceptInvitation($id)
    {
        try {
            Invitation::where('user_id', Auth::user()->id)
                ->where('project_id', $id)
                ->update(['status' => 1]);
            Alert::success('Invitation Accepted', 'Congrats! You\'re now a part of the project');
            return redirect()->back();
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function rejectInvitation($id)
    {
        try {
            Invitation::where('user_id', Auth::user()->id)
                ->where('project_id', $id)
                ->delete();
            Project::where('id', Auth::user()->id)
                ->where('project_id', $id)
                ->delete();
            Alert::error('Invitation Declined', 'You rejected the invitation request');
            return redirect()->back();
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
