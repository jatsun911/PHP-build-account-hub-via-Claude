<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ledger_groups', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropUnique(['entity_id', 'name']);
            $table->string('entity_id')->nullable()->change();
            $table->foreign('entity_id')->references('id')->on('entities')->cascadeOnDelete();
            // NULL entity_id = system-wide group (Suspense, etc.)
            // Per-entity uniqueness is enforced at application level
        });
    }

    public function down(): void
    {
        Schema::table('ledger_groups', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->string('entity_id')->nullable(false)->change();
            $table->foreign('entity_id')->references('id')->on('entities')->cascadeOnDelete();
            $table->unique(['entity_id', 'name']);
        });
    }
};
