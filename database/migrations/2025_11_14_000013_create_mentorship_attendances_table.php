<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create mentorship_attendances table.
 *
 * Stores attendance records for individual mentorship sessions, including
 * whether an absence was notified in advance.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mentorship_attendances', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('mentorship_session_id')
                ->constrained('mentorship_sessions')
                ->cascadeOnDelete();

            $table->string('status', 32);
            $table->boolean('absence_notified')->default(false);

            $table->timestamps();

            $table->index(
                ['mentorship_session_id', 'status'],
                'mentorship_attendances_session_status_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_attendances');
    }
};
