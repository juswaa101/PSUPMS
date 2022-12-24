<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'task_id', 'project_id'
    ];
}
