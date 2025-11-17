<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create class_enrollments table.
 *
 * Stores student memberships in class groups.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_enrollments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('class_group_id')
                ->constrained('class_groups');

            $table->foreignId('student_id')
                ->constrained('students');

            $table->timestamp('enrolled_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['class_group_id', 'student_id'],
                'class_enrollments_group_student_unique'
            );

            $table->index(['student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
