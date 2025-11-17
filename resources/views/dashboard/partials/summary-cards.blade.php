{{-- resources/views/dashboard/partials/summary-cards.blade.php --}}

@php
    /**
     * Expected variables in scope:
     *
     * @var int|null $studentsCount
     * @var int|null $teachersCount
     * @var int|null $subjectsCount
     * @var int|null $classGroupsCount
     * @var int|null $mentorshipsCount
     */
@endphp

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6">
    {{-- Students --}}
    <div
        class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
               border border-border-subtle dark:border-border-inverse px-4 py-4"
    >
        <div class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
            Students
        </div>
        <div class="mt-2 text-2xl font-semibold text-text dark:text-text-inverse tabular-nums">
            {{ $studentsCount ?? '—' }}
        </div>
    </div>

    {{-- Teachers --}}
    <div
        class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
               border border-border-subtle dark:border-border-inverse px-4 py-4"
    >
        <div class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
            Teachers
        </div>
        <div class="mt-2 text-2xl font-semibold text-text dark:text-text-inverse tabular-nums">
            {{ $teachersCount ?? '—' }}
        </div>
    </div>

    {{-- Subjects --}}
    <div
        class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
               border border-border-subtle dark:border-border-inverse px-4 py-4"
    >
        <div class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
            Subjects
        </div>
        <div class="mt-2 text-2xl font-semibold text-text dark:text-text-inverse tabular-nums">
            {{ $subjectsCount ?? '—' }}
        </div>
    </div>

    {{-- Class groups --}}
    <div
        class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
               border border-border-subtle dark:border-border-inverse px-4 py-4"
    >
        <div class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
            Class groups
        </div>
        <div class="mt-2 text-2xl font-semibold text-text dark:text-text-inverse tabular-nums">
            {{ $classGroupsCount ?? '—' }}
        </div>
    </div>

    {{-- Mentorships --}}
    <div
        class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
               border border-border-subtle dark:border-border-inverse px-4 py-4"
    >
        <div class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
            Mentorships
        </div>
        <div class="mt-2 text-2xl font-semibold text-text dark:text-text-inverse tabular-nums">
            {{ $mentorshipsCount ?? '—' }}
        </div>
    </div>
</div>
