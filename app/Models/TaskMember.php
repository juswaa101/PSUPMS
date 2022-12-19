<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TaskMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'task_id'
    ];
}
