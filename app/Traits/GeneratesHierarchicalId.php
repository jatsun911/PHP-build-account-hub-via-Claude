<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait GeneratesHierarchicalId
{
    /**
     * Boot the trait to attach the creating event.
     */
    protected static function bootGeneratesHierarchicalId()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = $model->generateCustomId();
            }
        });
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Generate the custom ID based on the model type.
     */
    protected function generateCustomId()
    {
        $table = $this->getTable();
        
        if ($table === 'users') {
            // Get the next user sequence
            $count = DB::table('users')->count() + 1;
            // 1.XXXXXXXXXX
            $randomString = strtoupper(Str::random(10));
            return "{$count}.{$randomString}";
        }
        
        if ($table === 'entities') {
            // [UserID].E[10000+count]
            $userId = $this->created_by_user_id; // UUID of user
            $user = DB::table('users')->where('id', $userId)->first();
            $userSerial = $user ? $user->serial : 'SYSTEM';
            
            $count = DB::table('entities')->where('created_by_user_id', $userId)->count() + 1;
            $sequence = 10000 + $count;
            return "{$userSerial}.E{$sequence}";
        }
        
        if ($table === 'ledger_groups') {
            // [EntityID].G[10000+count]
            $entityId = $this->entity_id;
            $count = DB::table('ledger_groups')->where('entity_id', $entityId)->count() + 1;
            $sequence = 10000 + $count;
            return "{$entityId}.G{$sequence}";
        }
        
        if ($table === 'ledgers') {
            // [EntityID].L[100000+count]
            $entityId = $this->entity_id;
            $count = DB::table('ledgers')->where('entity_id', $entityId)->count() + 1;
            $sequence = 100000 + $count;
            return "{$entityId}.L{$sequence}";
        }
        
        // Fallback to standard UUID for other tables using this trait (e.g., transactions)
        return (string) Str::uuid();
    }
}
