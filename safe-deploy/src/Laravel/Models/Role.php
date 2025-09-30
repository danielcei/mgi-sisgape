<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use SafeDeploy\Laravel\Concerns\ResetsTaggedCache;
use SafeDeploy\Laravel\Concerns\Userstamps;
use SafeDeploy\Laravel\Contracts\Userstampable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole implements Auditable, Userstampable
{
    use HasAudits;
    use ResetsTaggedCache;
    use SoftDeletes;
    use Userstamps;
}
