<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Seed system ledgers for a specific entity. Call after entity creation.
     * If $entityId is null, seeds for ALL entities that lack system ledgers.
     */
    public function run(?string $entityId = null): void
    {
        $suspenseGroup = \App\Models\LedgerGroup::firstOrCreate(
            ['name' => 'Suspense (default)', 'entity_id' => null],
            ['type' => 'equity']
        );

        $entities = $entityId
            ? \App\Models\Entity::where('id', $entityId)->get()
            : \App\Models\Entity::all();

        foreach ($entities as $entity) {
            \App\Models\Ledger::firstOrCreate(
                ['name' => 'Opening Balance Differences A/c(System)', 'entity_id' => $entity->id],
                [
                    'code' => 'OB_DIFF_SYS',
                    'type' => 'equity',
                    'ledger_group_id' => $suspenseGroup->id,
                    'description' => 'System account for balancing opening entries.',
                ]
            );

            \App\Models\Ledger::firstOrCreate(
                ['name' => 'Suspense A/c(System)', 'entity_id' => $entity->id],
                [
                    'code' => 'SUSPENSE_SYS',
                    'type' => 'equity',
                    'ledger_group_id' => $suspenseGroup->id,
                    'description' => 'Default ledger for unsorted parsed transactions.',
                ]
            );
        }
    }
}
