<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'board_id' => $this->board_id,
            'name' => $this->name,
            'description' => $this->description,
            'task_start_date' => $this->task_start_date,
            'task_due_date' => $this->task_due_date,
            'privacy_status' => $this->privacy_status,
            'total_subtask' => DB::table('task_progress')->where('task_id', $this->id)->first(),
            'total_subtask_done' => DB::table('task_progress')->where('task_id', $this->id)->first(),
            'task_members' => DB::table('task_members')->where('task_id', $this->id)->select(['task_members.*', 'task_members.user_id as uid'])->get(),
            'subtasks' => DB::table('tasks')->join('subtasks', 'subtasks.task_id', '=', 'tasks.id')->where('subtasks.task_id', $this->id)->
                        select(['subtasks.*', 'subtasks.board_id as bid', 'subtasks.task_id as task_id', 'tasks.*'])->get(),
            'color' => DB::table('task_colors')->where('task_id', $this->id)->first() == null ? "#FFFFFF"
                : DB::table('task_colors')->where('task_id', $this->id)->first()
        ];
    }
}
