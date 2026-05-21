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
        Schema::create('entities', function (Blueprint $table) {
            $table->string('id')->primary(); // Custom Hierarchical ID: {UserSerial}.E{Number}
            $table->string('name');
            $table->string('owner_name');
            $table->string('email');
            $table->string('mobile');
            $table->enum('constitution', ['Proprietorship', 'Partnership', 'LLP', 'Company', 'AOP', 'Trust', 'Other']);
            $table->string('pan')->nullable();
            $table->string('gstin')->nullable();
            $table->boolean('is_msme')->default(false);
            $table->string('msme_no')->nullable();
            $table->date('msme_date')->nullable();
            $table->text('address')->nullable();
            $table->enum('nature_of_business', ['Service', 'Trading', 'Manufacturing']);
            
            $table->foreignUuid('created_by_user_id')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
