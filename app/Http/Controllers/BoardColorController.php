<?php

namespace App\Http\Controllers;

use App\Models\BoardColor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardColorController extends Controller
{
    public function getBoardColor($id)
    {
        try {
            $getBoardColor = BoardColor::where('user_id', Auth::user()->id)
                ->where('project_id', $id)->first();
            return response()->json($getBoardColor);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function updateBoardColor(Request $request, $id)
    {
        try {
            BoardColor::updateOrCreate(
                [
                    'project_id' => $id,
                    'user_id' => Auth::user()->id,
                ],
                [
                    'project_id' => $id,
                    'user_id' => Auth::user()->id,
                    'board_color' => $request->input('board_color')
                ]
            );
            return response()->json('Board Color Updated!');
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
