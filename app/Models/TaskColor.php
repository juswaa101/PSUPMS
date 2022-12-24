<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'task_color', 'project_id'
    ];
}
