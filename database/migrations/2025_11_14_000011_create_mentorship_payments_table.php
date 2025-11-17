<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create mentorship_payments table.
 *
 * Stores monetary payments associated with mentorships and
 * their corresponding hour credits.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mentorship_payments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('mentorship_id')
                ->constrained('mentorships')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->unsignedInteger('hours');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['mentorship_id'], 'mentorship_payments_mentorship_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_payments');
    }
};
