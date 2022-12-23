<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardProgress extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'board_id', 'total_task', 'total_task_done'
    ];
}
