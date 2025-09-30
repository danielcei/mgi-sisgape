<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as HasAudits;
use OwenIt\Auditing\Contracts\Auditable;
use SafeDeploy\Laravel\Concerns\ResetsTaggedCache;
use SafeDeploy\Laravel\Concerns\Userstamps;
use SafeDeploy\Laravel\Contracts\Userstampable;

/**
 * @property int|string|null $created_by
 * @property int|string|null $deleted_by
 * @property int|string|null $updated_by
 */
abstract class BaseModel extends Model implements Auditable, Userstampable
{
    use HasAudits;
    use ResetsTaggedCache;
    use SoftDeletes;
    use Userstamps;
}
