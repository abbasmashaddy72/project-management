<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    use HasTenantScope;
}
