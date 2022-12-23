<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttachedFile extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['task_id', 'user_id', 'filepath'];
}
