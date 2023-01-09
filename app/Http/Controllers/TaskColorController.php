<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\TaskColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskColorController extends Controller
{
    public function updateTaskColor(Request $request, $id)
    {
        try {
            TaskColor::updateOrCreate(
                [
                    'task_id' => $id,
                ],
                [
                    'task_id' => $id,
                    'user_id' => Auth::user()->id,
                    'task_color' => $request->input('task_color')
                ]
            );
            return response()->json('Task Color Updated!');
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
