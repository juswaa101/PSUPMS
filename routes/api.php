<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//  Board API Routes
Route::get('boards/{user_id}/{project_id}', [BoardController::class, 'index']);
Route::get('board/{id}/{user_id}/{project_id}', [BoardController::class, 'show']);
Route::post('board/{user_id}/{project_id}', [BoardController::class, 'store']);
Route::put('board/{user_id}/{project_id}', [BoardController::class, 'update']);
Route::delete('board/{id}/{user_id}/{project_id}', [BoardController::class, 'destroy']);

//  Task API Routes
Route::get('tasks/{user_id}/{project_id}', [TaskController::class, 'index']);
Route::get('task/{id}/{user_id}/{project_id}', [TaskController::class, 'show']);
Route::post('task/{user_id}/{project_id}', [TaskController::class, 'store']);
Route::put('task/{user_id}/{project_id}', [TaskController::class, 'update']);
Route::delete('task/{id}/{user_id}/{project_id}', [TaskController::class, 'destroy']);
