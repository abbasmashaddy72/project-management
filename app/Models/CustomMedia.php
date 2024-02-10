<?php

namespace App\Models;

use App\Traits\MultiTenancy;
use Awcodes\Curator\Models\Media;

class CustomMedia extends Media
{
    use MultiTenancy;

    protected $table = 'media';
}
