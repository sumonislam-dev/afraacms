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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->string('session', 20);
            $table->string('roll_number', 50)->nullable();
            $table->string('registration_number', 50)->nullable();
            $table->date('admission_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('grade', 10)->nullable();
            $table->decimal('grade_point', 4, 2)->nullable();
            $table->decimal('grade_scale', 4, 2)->nullable();
            $table->string('result_status', 20)->default('pending');
            $table->string('certificate_number', 50)->nullable()->unique();
            $table->string('verification_code', 100)->nullable()->unique();
            $table->string('certificate_status', 20)->default('not_issued');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_id', 'session']);
            $table->index('certificate_status');
            $table->unique(['course_id', 'session', 'roll_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
