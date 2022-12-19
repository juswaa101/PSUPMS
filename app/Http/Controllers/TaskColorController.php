<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\TaskColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskColorController extends Controller
{
    public function getTaskColor($id)
    {
        try {
            $getTaskColor = TaskColor::where('user_id', Auth::user()->id)
                ->where('project_id', $id)->first();
            return response()->json($getTaskColor);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function updateTaskColor(Request $request, $id)
    {
        try {
            TaskColor::updateOrCreate(
                [
                    'project_id' => $id,
                    'user_id' => Auth::user()->id,
                ],
                [
                    'project_id' => $id,
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
