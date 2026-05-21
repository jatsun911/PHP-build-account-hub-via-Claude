<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    use \App\Traits\GeneratesHierarchicalId;

    protected $fillable = [
        'entity_id',
        'original_filename',
        'file_path',
        'status',
        'extracted_data',
        'uploaded_by'
    ];

    protected $casts = [
        'extracted_data' => 'array',
    ];
}
