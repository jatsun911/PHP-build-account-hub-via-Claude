<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Suspense group exists
        $suspenseGroup = \App\Models\LedgerGroup::firstOrCreate(['name' => 'Suspense (default)'], ['type' => 'equity']);

        // Opening Balance Differences A/c(System)
        \App\Models\Ledger::firstOrCreate(
            ['name' => 'Opening Balance Differences A/c(System)'],
            [
                'code' => 'OB_DIFF_SYS',
                'type' => 'equity',
                'ledger_group_id' => $suspenseGroup->id,
                'description' => 'System account for balancing opening entries. Cannot be deleted.',
            ]
        );

        // Suspense A/c(System)
        \App\Models\Ledger::firstOrCreate(
            ['name' => 'Suspense A/c(System)'],
            [
                'code' => 'SUSPENSE_SYS',
                'type' => 'equity',
                'ledger_group_id' => $suspenseGroup->id,
                'description' => 'Default ledger for unsorted parsed transactions. Cannot be deleted.',
            ]
        );
    }
}
