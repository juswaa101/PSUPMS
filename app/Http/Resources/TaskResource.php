<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'board_id' => $this->board_id,
            'name' => $this->name,
            'description' => $this->description,
            'task_start_date' => $this->task_start_date,
            'task_due_date' => $this->task_due_date,
            'privacy_status' => $this->privacy_status,
            'total_subtask_done' => $this->total_subtask_done,
            'total_subtask' => $this->total_subtask
        ];
    }
}
