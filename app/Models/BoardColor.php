<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'board_color', 'project_id'
    ];
}
