<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkMode extends Model
{
    protected $fillable = ['name'];

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'work_mode_profile');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'work_mode_id');
    }
}

