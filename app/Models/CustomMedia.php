<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Awcodes\Curator\Models\Media;

class CustomMedia extends Media
{
    use HasTenantScope;

    protected $table = 'media';
}
