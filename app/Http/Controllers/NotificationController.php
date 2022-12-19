<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * @throws Throwable
     */
    public function dismissNotification($id)
    {
        try {
            Notification::firstWhere('id', $id)->deleteOrFail();
            return redirect()->back();
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function markAsReadNotification($id)
    {
        try {
            Notification::firstWhere('id', $id)->update([
                'has_read' => true,
            ]);
            return redirect()->back();
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function markAsUnreadNotification($id)
    {
        try {
            Notification::firstWhere('id', $id)->update([
                'has_read' => false,
            ]);
            return redirect()->back();
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function markAllNotificationAsRead()
    {
        try {
            Notification::where('has_read', '=', 0)
                ->where('user_id', Auth::user()->id)
                ->update(['has_read' => true]);
            return redirect()->back();
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function countUnreadNotification()
    {
        try {
            $totalNotification = Notification::where('user_id', Auth::user()->id)
                ->where('has_read', '=', 0)
                ->get()
                ->count();

            return response()->json($totalNotification);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
