<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create mentorship_debits table.
 *
 * Stores hour debits for mentorships originated from specific mentorship sessions.
 * Enforces idempotency via a composite unique key.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mentorship_debits', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('mentorship_id')
                ->constrained('mentorships')
                ->cascadeOnDelete();

            $table->foreignId('mentorship_session_id')
                ->constrained('mentorship_sessions')
                ->cascadeOnDelete();

            // >= 0 at schema level; > 0 garantido pelas regras de domínio/Service
            $table->unsignedInteger('hours')
                ->comment('Whole hours debited for the related mentorship session.');

            $table->timestamp('debited_at')->nullable();
            $table->timestamps();

            // Query helpers
            $table->index(['mentorship_id'], 'mentorship_debits_mentorship_index');
            $table->index(['mentorship_session_id'], 'mentorship_debits_session_index');

            // Idempotency: prevent multiple debits for the same mentorship-session pair
            $table->unique(
                ['mentorship_id', 'mentorship_session_id'],
                'mentorship_debits_mentorship_session_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_debits');
    }
};
