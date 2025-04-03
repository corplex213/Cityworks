<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTemplate extends Model
{
    protected $table = 'project_template';

    protected $fillable = [
        'text',
        'key_persons',
        'status',
        'start_date',
        'due_date',
        'comments',
    ];

    protected $casts = [
        'key_persons' => 'array', 
    ];
}
