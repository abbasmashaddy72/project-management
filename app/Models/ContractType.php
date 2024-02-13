<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractType extends Model
{
    use HasFactory, SoftDeletes, HasTenantScope;

    protected $fillable = [
        'name',
        'description',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];
}
