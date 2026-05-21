<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ledger_groups', function (Blueprint $table) {
            $table->string('id')->primary(); // Custom ID: {EntityID}.G{Number}
            $table->string('entity_id');
            $table->foreign('entity_id')->references('id')->on('entities')->cascadeOnDelete();
            
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->timestamps();
            
            $table->unique(['entity_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_groups');
    }
};
