<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'entity_id', 'role'])]
class EntityUser extends Pivot
{
    //
}
