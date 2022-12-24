<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardProgress extends Model
{
    use HasFactory;
    protected $fillable = [
        'board_id', 'total_task', 'total_task_done'
    ];
}
