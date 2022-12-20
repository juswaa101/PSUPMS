<?php

namespace App\Http\Controllers;

use App\Board;
use Exception;
use App\Models\User;
use App\Models\Report;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\BoardProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BoardResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index($user_id)
    {
        try {
            //  fetch all boards based on current user id
            $project = Project::findOrFail(request()->segment(4));
            $findProject = Project::where('project_title', $project->project_title)->get();
            $project_id = [];
            foreach ($findProject as $item) {
                $project_id[] = $item->project_id;
            }
            $boards = DB::table('boards')->orderBy('index')->whereIn('project_id', $project_id)->get();

            //  return boards as a resource
            return BoardResource::collection($boards);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $user_id
     * @return BoardResource|JsonResponse|void
     */
    public function store(Request $request, $user_id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required',
            ]);
    
            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                $project = Project::where('project_id', $request->segment(4))->first();
                $board_count = DB::table('boards')->join('projects', 'projects.project_id', '=', 'boards.project_id')
                    ->where('projects.project_title', '=', $project->project_title)
                    ->get();
    
                if ($board_count->count() < 7) {
                    //  store new board in the database
                    $board = new Board;
                    $board->user_id = $user_id;
                    $board->project_id = $request->segment(4);
                    $board->name = $request->input('name');
    
                    $userName = User::firstWhere('id', $user_id);
    
                    if ($board->save()) {
                        // Create Board Progress
                        BoardProgress::create([
                            'board_id' => $board->id,
                            'total_task' => 0,
                            'total_task_done' => 0
                        ]);

                        $users = Project::where('project_id', $request->segment(4))->first();
                        $all_users = Project::where('project_title', $users->project_title)->get();
                        Report::create(['user_id' => $user_id, 'project_id' => $request->segment(4), 'message' => ' assigned a board in ' . $project->project_title]);
                        foreach ($all_users as $user) {
                            $user = User::where('id', $user->id)->first();
                            Notification::create([
                                'user_id' => $user->id,
                                'notification_message' => $userName->name . ' created a board: ' . $request->input('name') . ' in ' . $users->project_title,
                            ]);
                        }
    
                        //  if saved then return board as a resource
                        return new BoardResource($board);
                    }
                } else {
                    return response()->json([
                        'board_count_error' => 'Maximum of 7 boards only per projects',
                        400
                    ]);
                }
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @param $user_id
     * @return BoardResource
     */
    public function show($id, $user_id)
    {
        try {
            //  fetch board based on current user id and board id
            $project = Project::findOrFail(request()->segment(5));
            $findProject = Project::all()->where('project_title', $project->project_title);
            $project_id = [];
            foreach ($findProject as $item) {
                $project_id[] = $item->project_id;
            }

            $board = Board::whereIn('project_id', $project_id)->findOrFail($id);

            //  return board as a resource
            return new BoardResource($board);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Board $board
     * @return void
     */
    public function edit(Board $board)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $user_id
     * @return BoardResource|JsonResponse
     */
    public function update(Request $request, $user_id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required',
            ]);
    
            if ($validate->fails()) {
                return response()->json($validate->getMessageBag(), 400);
            } else {
                //  update existing board data based on current user id and board id
                $currentValue = $request->input('name');
                $board = Board::findOrFail($request->input('board_id'));
                $board->user_id = $user_id;
                $board->project_id = $request->segment(4);
                $board->name = $request->input('name');
    
                $userName = User::firstWhere('id', $user_id);

                if ($board->save()) {
                    $users = Project::where('project_id', $request->segment(4))->first();
                    $all_users = Project::where('project_title', $users->project_title)->get();
    
                    Report::create(['user_id' => $user_id, 'project_id' => $request->segment(4), 'message' => ' updated the board in ' . $users->project_title]);
                    foreach ($all_users as $user) {
                        $user = User::where('id', $user->id)->first();
                        Notification::create([
                            'user_id' => $user->id,
                            'notification_message' => $userName->name . ' updated the board: ' . $currentValue . ' in ' . $users->project_title,
                        ]);
                    }
                    //  if saved then return board as a resource
                    return new BoardResource($board);
                }
            }
        } catch (Exception $e)   {
            abort_if($e, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @param $user_id
     * @return BoardResource|void
     */
    public function destroy($id, $user_id)
    {
        try {
            //  fetch board based on current user id and board id
            $board = Board::findOrFail($id);
            Report::create(['user_id' => $user_id,  'project_id' => $board->project_id, 'message' => ' deleted a board: ' . $board->name]);

            $filterProject = Project::firstWhere('project_id', $board->project_id);
            $project = Project::where('project_title', $filterProject->project_title)->get();
            $user = User::firstWhere('id', $user_id);

            foreach ($project as $p) {
                Notification::create([
                    'user_id' => $p->id,
                    'notification_message' => $user->name . ' deleted the board: ' . $board->name . ' in ' . $filterProject->project_title,
                ]);
            }

            if ($board->delete()) {
                //  id deleted then return board as a resource
                return new BoardResource($board);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            $board_id = $request->input('boardIds');

            for ($i = 0; $i < count($board_id); $i++) {
                Board::where('id', $board_id[$i])->update(['index' => $i]);
            }

            return response('Order Has Been Updated!', 200);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
