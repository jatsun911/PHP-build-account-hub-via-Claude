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
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->string('id')->primary(); // Standard UUID or custom
            $table->string('entity_id');
            $table->foreign('entity_id')->references('id')->on('entities')->cascadeOnDelete();
            
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('status')->default('pending');
            $table->json('extracted_data')->nullable();
            
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statements');
    }
};
