<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDetail extends Model
{
    protected $table = 'project_details';

    protected $fillable = [
        'text',
        'key_persons',
        'status',
        'start_date',
        'due_date',
        'comments',
        'file_upload',
        'budget',
    ];
}
