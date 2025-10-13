<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name', 'skill_category_id'];

    public function category()
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_skill');
    }
}

