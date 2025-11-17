<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create mentorship_sessions table.
 *
 * Stores individual mentorship sessions with date, time, duration and status.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mentorship_sessions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('mentorship_id')
                ->constrained('mentorships')
                ->cascadeOnDelete();

            $table->date('session_date');
            $table->time('start_time');
            $table->unsignedInteger('duration_minutes');
            $table->string('status', 32);

            $table->timestamps();

            $table->index(
                ['mentorship_id', 'session_date'],
                'mentorship_sessions_mentorship_date_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_sessions');
    }
};
