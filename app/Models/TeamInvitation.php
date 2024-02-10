<?php

namespace App\Models;

use App\Traits\MultiTenancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamInvitation extends Model
{
    use HasFactory, MultiTenancy;

    protected $fillable = [
        'email'
    ];
}
