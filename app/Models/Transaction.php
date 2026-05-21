<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use \App\Traits\GeneratesHierarchicalId;

    protected $fillable = [
        'ledger_id',
        'amount',
        'type',
        'description',
        'transaction_date',
        'attached_document_path',
        'status',
        'created_by',
    ];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }
}
