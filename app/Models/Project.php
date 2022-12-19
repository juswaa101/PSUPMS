<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $primaryKey = 'project_id';
    protected $fillable = [
        'project_title',
        'project_description',
        'project_start_date',
        'project_end_date',
        'id',
        'is_project_head',
        'create_task_status',
        'create_subtask_status',
        'is_finished',
        'template'
    ];
}
