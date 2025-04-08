<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = [
        'proj_name',
        'location',
        'description',
        'status',
    ];

    public function details()
    {
        return $this->hasOne(Detail::class);
    }
}
