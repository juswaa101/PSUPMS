<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, SoftDeletes;
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

    protected  static  function  boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}
