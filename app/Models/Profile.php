<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'photo_url',
        'linkedin_url',
        'github_url',
        'portfolio_url',
        'summary',
        'years_of_experience'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'profile_skills');
    }

    public function jobContractTypes()
    {
        return $this->belongsToMany(JobContractType::class, 'job_contract_type_profiles');
    }

    public function workModes()
    {
        return $this->belongsToMany(WorkMode::class, 'work_mode_profiles');
    }
}
