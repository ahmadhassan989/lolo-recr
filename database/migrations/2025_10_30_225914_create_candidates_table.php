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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->text('skills')->nullable();
            $table->unsignedInteger('experience_years')->default(0);
            $table->text('notes')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality')->nullable();
            $table->string('education_level')->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->date('availability_date')->nullable();
            $table->string('source')->nullable();
            $table->tinyInteger('rating')->default(0);
            $table->enum('status', ['active', 'archived', 'blacklisted'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
