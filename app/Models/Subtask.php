<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'task_id', 'board_id', 'subtask_name',
        'subtask_description', 'subtask_start_date', 'subtask_due_date'
    ];
}
