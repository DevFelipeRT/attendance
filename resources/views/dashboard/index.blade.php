{{-- resources/views/dashboard/index.blade.php --}}

@php
    use App\Enums\ClassLessonStatus;

    /**
     * Expected variables (all optional, the view handles missing values with fallbacks):
     *
     * @var int|null $studentsCount
     * @var int|null $teachersCount
     * @var int|null $subjectsCount
     * @var int|null $classGroupsCount
     * @var int|null $mentorshipsCount
     * @var \Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|null $upcomingLessons
     * @var int|null $upcomingLessonsCount
     * @var \Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|null $upcomingSessions
     * @var int|null $upcomingSessionsCount
     * @var \Illuminate\Support\Collection|null $lowBalanceMentorships
     * @var \Illuminate\Support\Collection|null $classGroupAlerts
     */
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Main summary cards --}}
        @include('dashboard.partials.summary-cards')

        {{-- Bottom row: upcoming lessons + alerts + upcoming sessions --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                @include('dashboard.partials.upcoming-lessons-card')
                @include('dashboard.partials.upcoming-sessions-card')
            </div>

            <div class="space-y-6">
                @include('dashboard.partials.class-group-alerts-card')
                @include('dashboard.partials.mentorship-alerts-card')
            </div>
        </div>
    </div>
</x-app-layout>
