<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use \App\Traits\GeneratesHierarchicalId;

    protected $fillable = [
        'serial',
        'name',
        'owner_name',
        'email',
        'mobile',
        'constitution',
        'pan',
        'gstin',
        'is_msme',
        'msme_no',
        'msme_date',
        'address',
        'nature_of_business',
        'created_by_user_id',
        'accounting_period_year'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }
}
