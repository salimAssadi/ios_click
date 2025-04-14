<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Tenant\Traits\BelongsToTenant;

abstract class TenantModel extends Model
{
    use BelongsToTenant;
}
