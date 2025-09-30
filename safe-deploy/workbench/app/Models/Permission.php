<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use SafeDeploy\Laravel\Models\Permission as Model;
use Workbench\Database\Factories\PermissionFactory;

class Permission extends Model
{
    /** @use HasFactory<PermissionFactory> */
    use HasFactory;
}
