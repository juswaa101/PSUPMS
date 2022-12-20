<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
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
            'name' => $this->name,
            'index' => $this->index,
            'board_progress' => DB::table('board_progress')->where('board_id', $this->id)->first(),
            'board_done' => DB::table('board_progress')->where('board_id', $this->id)->first()
        ];
    }
}
