<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LedgerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name' => 'Fixed Assets', 'type' => 'asset'],
            ['name' => 'Investments', 'type' => 'asset'],
            ['name' => 'Current Asset', 'type' => 'asset'],
            ['name' => 'Non Current Asset', 'type' => 'asset'],
            ['name' => 'Misc Asset', 'type' => 'asset'],
            
            ['name' => 'Capital', 'type' => 'liability'],
            ['name' => 'Reserves & Surplus', 'type' => 'liability'],
            ['name' => 'Current Liability', 'type' => 'liability'],
            ['name' => 'Non Current Liability', 'type' => 'liability'],
            ['name' => 'Misc Liability', 'type' => 'liability'],
            
            ['name' => 'Suspense (default)', 'type' => 'equity'], // Often treated as equity/liability placeholder
        ];

        foreach ($groups as $group) {
            \App\Models\LedgerGroup::firstOrCreate(['name' => $group['name']], ['type' => $group['type']]);
        }
    }
}
