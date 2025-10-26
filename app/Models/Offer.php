<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'company', 'location', 'url', 'description',
        'external_id', 'source', 'company_logo_url',
        'salary_min', 'salary_max', 'is_active',
        'scraped_at', 'published_at', 'raw_data',
        'job_contract_type_id', 'work_mode_id'
    ];

    protected $casts = [
        'raw_data' => 'array',
        'scraped_at' => 'datetime',
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function contractType()
    {
        return $this->belongsTo(\App\Models\JobContractType::class, 'job_contract_type_id');
    }

    public function workMode()
    {
        return $this->belongsTo(\App\Models\WorkMode::class, 'work_mode_id');
    }
}
