<?php

namespace App\Models;

use Spatie\Permission\Models\Role as PermissionRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends PermissionRole
{
    use HasFactory;
}
