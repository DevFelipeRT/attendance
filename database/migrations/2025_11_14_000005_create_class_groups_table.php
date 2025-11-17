<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create class_groups table.
 *
 * Stores configuration and scheduling data for regular classes
 * and one-to-one mentorship groups.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_groups', function (Blueprint $table): void {
            $table->id();

            $table->string('name');

            $table->foreignId('subject_id')
                ->constrained('subjects');

            $table->foreignId('teacher_id')
                ->constrained('teachers');

            $table->date('term_start_date')->nullable();
            $table->date('term_end_date')->nullable();

            $table->unsignedSmallInteger('default_lesson_duration_minutes')->nullable();

            $table->json('weekly_schedule')->nullable();

            $table->decimal('hourly_rate', 10, 2)->nullable();

            $table->timestamps();

            $table->index(['subject_id']);
            $table->index(['teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_groups');
    }
};
