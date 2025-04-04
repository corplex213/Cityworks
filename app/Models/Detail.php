<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $fillable = [
        'text',
        'key_persons',
        'status',
        'start_date',
        'due_date',
        'comments',
        'budget',
        'file_upload',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
