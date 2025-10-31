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
        Schema::create('tbljobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->text('skills')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship'])->default('full_time');
            $table->string('salary_range')->nullable();
            $table->date('deadline')->nullable();
            $table->enum('status', ['open', 'closed', 'draft'])->default('open');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbljobs');
    }
};
