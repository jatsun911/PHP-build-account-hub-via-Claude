<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use \App\Traits\GeneratesHierarchicalId;

    protected $fillable = [
        'entity_id',
        'name',
        'code',
        'type',
        'description',
        'ledger_group_id',
    ];

    public function ledgerGroup()
    {
        return $this->belongsTo(LedgerGroup::class);
    }
}
