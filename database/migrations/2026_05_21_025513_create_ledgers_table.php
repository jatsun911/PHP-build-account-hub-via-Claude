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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->string('id')->primary(); // Custom ID: {EntityID}.L{Number}
            $table->string('entity_id');
            $table->foreign('entity_id')->references('id')->on('entities')->cascadeOnDelete();
            
            $table->string('ledger_group_id')->nullable();
            $table->foreign('ledger_group_id')->references('id')->on('ledger_groups')->nullOnDelete();
            
            $table->string('name');
            $table->string('code')->nullable(); // Optional alias
            $table->string('type');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['entity_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
