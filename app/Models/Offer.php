<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'location',
        'url',
        'contract_type_id',
        'work_mode_id',
        'description',
    ];

    // Relations
    public function contractType()
    {
        return $this->belongsTo(JobContractType::class, 'contract_type_id');
    }

    public function workMode()
    {
        return $this->belongsTo(WorkMode::class, 'work_mode_id');
    }
}
