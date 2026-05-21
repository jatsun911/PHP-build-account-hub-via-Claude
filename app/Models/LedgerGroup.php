<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerGroup extends Model
{
    use \App\Traits\GeneratesHierarchicalId;

    protected $fillable = ['entity_id', 'name', 'type'];

    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }
    
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
