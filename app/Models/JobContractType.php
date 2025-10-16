<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobContractType extends Model
{
    protected $fillable = ['name'];

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'job_contract_type_profile');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'job_contract_type_id');
    }
}
