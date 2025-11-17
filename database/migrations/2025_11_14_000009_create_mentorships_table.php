<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create mentorships table.
 *
 * Stores one-to-one mentorship agreements between a student and a teacher,
 * with an optional subject and its own hourly rate.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mentorships', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->nullable()
                ->constrained('subjects')
                ->nullOnDelete();

            $table->decimal('hourly_rate', 10, 2);
            $table->string('status', 32)->default('active');

            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();

            $table->timestamps();

            $table->index(
                ['student_id', 'teacher_id', 'status'],
                'mentorships_student_teacher_status_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorships');
    }
};
