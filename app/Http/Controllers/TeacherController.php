<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * HTTP controller for managing teachers and their paired user accounts.
 */
class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index(): View
    {
        $teachers = Teacher::query()
            ->with('user')
            ->orderBy('name')
            ->get();

        return view('teachers.index', [
            'teachers' => $teachers,
        ]);
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(): View
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created teacher and paired user account.
     */
    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = $this->createTeacherUser($data);

        $teacher = Teacher::query()->create([
            'name'    => $data['name'],
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('teachers.edit', $teacher)
            ->with('status', 'Teacher successfully created.');
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('teachers.edit', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Update the specified teacher and paired user account.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $data = $request->validated();

        $teacher->update([
            'name' => $data['name'],
        ]);

        if ($teacher->user instanceof User) {
            $updateUserData = [
                'name' => $data['name'],
            ];

            if (! empty($data['email'] ?? null)) {
                $updateUserData['email'] = $data['email'];
            }

            if (! empty($data['password'] ?? null)) {
                $updateUserData['password'] = Hash::make($data['password']);
            }

            $teacher->user->update($updateUserData);
        }

        return redirect()
            ->route('teachers.edit', $teacher)
            ->with('status', 'Teacher successfully updated.');
    }

    /**
     * Remove the specified teacher and paired user account.
     */
    public function destroy(Teacher $teacher): RedirectResponse
    {
        $user = $teacher->user;

        $teacher->delete();

        if ($user instanceof User) {
            $user->delete();
        }

        return redirect()
            ->route('teachers.index')
            ->with('status', 'Teacher successfully deleted.');
    }

    /**
     * Create a user account for a teacher.
     */
    private function createTeacherUser(array $data): User
    {
        return User::query()->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => UserRole::Teacher,
        ]);
    }
}
