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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id')->primary(); // Standard UUID or custom ID
            $table->string('ledger_id');
            $table->foreign('ledger_id')->references('id')->on('ledgers')->cascadeOnDelete();
            
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['debit', 'credit']);
            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->string('attached_document_path')->nullable();
            $table->string('status')->default('completed'); // pending, completed
            
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
