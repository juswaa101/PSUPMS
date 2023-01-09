<?php

namespace App\Http\Controllers;

use App\Models\BoardColor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardColorController extends Controller
{
    public function updateBoardColor(Request $request, $id)
    {
        try {
            BoardColor::updateOrCreate(
                [
                    'board_id' => $id,
                ],
                [
                    'board_id' => $id,
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
