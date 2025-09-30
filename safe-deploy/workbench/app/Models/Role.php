<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use SafeDeploy\Laravel\Models\Role as Model;
use Workbench\Database\Factories\RoleFactory;

class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;
}
