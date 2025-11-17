<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seed additional users for development and testing.
 */
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedTeacherUsers();
    }

    /**
     * Create multiple teacher users and ensure paired teacher profiles.
     */
    private function seedTeacherUsers(): void
    {
        $teachers = User::factory()
            ->count(10)
            ->create([
                'role' => UserRole::Teacher,
            ]);

        $teachers->each(function (User $user): void {
            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                ['name'    => $user->name],
            );
        });
    }
}
