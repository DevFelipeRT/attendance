<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create attendances table.
 *
 * Stores attendance records for students in specific class lessons.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('class_lesson_id')
                ->constrained('class_lessons');

            $table->foreignId('student_id')
                ->constrained('students');

            $table->string('status', 32);
            $table->boolean('absence_notified')->default(false);

            $table->timestamps();

            $table->unique(
                ['class_lesson_id', 'student_id'],
                'attendances_lesson_student_unique'
            );

            $table->index(['student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
