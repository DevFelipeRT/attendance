<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create class_lessons table.
 *
 * Stores concrete class sessions for a given class group.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_lessons', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('class_group_id')
                ->constrained('class_groups');

            $table->date('lesson_date');
            $table->time('start_time')->nullable();

            $table->unsignedSmallInteger('duration_minutes');

            $table->string('status', 32);

            $table->timestamps();

            $table->index(['class_group_id', 'lesson_date'], 'class_lessons_group_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_lessons');
    }
};
