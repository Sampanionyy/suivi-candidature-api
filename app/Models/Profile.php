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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
