<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskProgress extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'board_id', 'task_id', 'total_subtask', 'total_subtask_done'
    ];
}
