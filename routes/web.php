<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ClassGroup\AttendanceController;
use App\Http\Controllers\ClassGroup\ClassEnrollmentController;
use App\Http\Controllers\ClassGroup\ClassGroupController;
use App\Http\Controllers\ClassGroup\ClassLessonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Mentorship\MentorshipAttendanceController;
use App\Http\Controllers\Mentorship\MentorshipController;
use App\Http\Controllers\Mentorship\MentorshipPaymentController;
use App\Http\Controllers\Mentorship\MentorshipSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function (): void {
    // Password
    Route::put('/password', [PasswordController::class, 'update'])
        ->name('password.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Students
    Route::resource('students', StudentController::class)
        ->except(['show']);

    // Teachers
    Route::resource('teachers', TeacherController::class)
        ->except(['show']);

    // Subjects
    Route::resource('subjects', SubjectController::class)
        ->except(['show']);

    // Class groups
    Route::resource('class-groups', ClassGroupController::class);

    // Class enrollments (by class group)
    Route::get('class-groups/{classGroup}/enrollments', [ClassEnrollmentController::class, 'index'])
        ->name('class-groups.enrollments.index');
    Route::post('class-groups/{classGroup}/enrollments', [ClassEnrollmentController::class, 'store'])
        ->name('class-groups.enrollments.store');
    Route::delete('class-groups/{classGroup}/enrollments/{enrollment}', [ClassEnrollmentController::class, 'destroy'])
        ->name('class-groups.enrollments.destroy');

    // Class lessons (by class group)
    Route::get('class-groups/{classGroup}/lessons', [ClassLessonController::class, 'index'])
        ->name('class-groups.lessons.index');
    Route::get('class-groups/{classGroup}/lessons/create', [ClassLessonController::class, 'create'])
        ->name('class-groups.lessons.create');
    Route::post('class-groups/{classGroup}/lessons', [ClassLessonController::class, 'store'])
        ->name('class-groups.lessons.store');
    Route::get('class-groups/{classGroup}/lessons/{lesson}/edit', [ClassLessonController::class, 'edit'])
        ->name('class-groups.lessons.edit');
    Route::put('class-groups/{classGroup}/lessons/{lesson}', [ClassLessonController::class, 'update'])
        ->name('class-groups.lessons.update');
    Route::post('class-groups/{classGroup}/lessons/{lesson}/cancel', [ClassLessonController::class, 'cancel'])
        ->name('class-groups.lessons.cancel');
    Route::delete('class-groups/{classGroup}/lessons/{lesson}', [ClassLessonController::class, 'destroy'])
        ->name('class-groups.lessons.destroy');
    Route::post('class-groups/{classGroup}/lessons/generate', [ClassLessonController::class, 'generate'])
        ->name('class-groups.lessons.generate');

    // Attendance (by class group)
    Route::get('class-groups/{classGroup}/lessons/{lesson}/attendance', [AttendanceController::class, 'edit'])
        ->name('class-groups.lessons.attendance.edit');
    Route::post('class-groups/{classGroup}/lessons/{lesson}/attendance', [AttendanceController::class, 'update'])
        ->name('class-groups.lessons.attendance.update');

    // Mentorships (root resource)
    Route::resource('mentorships', MentorshipController::class);

    // Mentorship sessions (nested in mentorships)
    Route::resource('mentorships.sessions', MentorshipSessionController::class);

    Route::get('mentorships/{mentorship}/sessions/{session}/edit', [MentorshipSessionController::class, 'edit'])
        ->name('mentorships.sessions.edit');

    // Mentorship session cancellation
    Route::post('mentorships/{mentorship}/sessions/{session}/cancel', [MentorshipSessionController::class, 'cancel'])
        ->name('mentorships.sessions.cancel');

    // Mentorship attendance (by session)
    Route::get('mentorships/{mentorship}/sessions/{session}/attendance', [MentorshipAttendanceController::class, 'edit'])
        ->name('mentorships.sessions.attendance.edit');
    Route::put('mentorships/{mentorship}/sessions/{session}/attendance', [MentorshipAttendanceController::class, 'update'])
        ->name('mentorships.sessions.attendance.update');

    // Mentorship payments (by mentorship)
    Route::get('mentorships/{mentorship}/payments', [MentorshipPaymentController::class, 'index'])
        ->name('mentorships.payments.index');
    Route::get('mentorships/{mentorship}/payments/create', [MentorshipPaymentController::class, 'create'])
        ->name('mentorships.payments.create');
    Route::post('mentorships/{mentorship}/payments', [MentorshipPaymentController::class, 'store'])
        ->name('mentorships.payments.store');
    Route::get('mentorships/{mentorship}/payments/{payment}', [MentorshipPaymentController::class, 'show'])
        ->name('mentorships.payments.show');
    Route::delete('mentorships/{mentorship}/payments/{payment}', [MentorshipPaymentController::class, 'destroy'])
        ->name('mentorships.payments.destroy');
});

require __DIR__ . '/auth.php';
