{{-- Mentorships: create form --}}
{{-- Creates a new mentorship contract. --}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            New mentorship
        </h1>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @if ($errors->any())
            <div
                class="mb-4 rounded-2xl border border-status-error-border
                       bg-status-error-softBg px-4 py-3 text-sm text-status-error-subtleFg"
            >
                Please fix the errors and try again.
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('mentorships.store') }}"
            class="bg-surface-base dark:bg-surface-inverse
                   rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse
                   p-6 space-y-4"
        >
            @csrf

            <div>
                <label
                    for="student_id"
                    class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                >
                    Student
                </label>
                <select
                    id="student_id"
                    name="student_id"
                    class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                           bg-surface-base dark:bg-surface-inverse
                           text-sm text-text dark:text-text-inverse
                           shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                >
                    <option value="">
                        Select a student
                    </option>
                    @foreach ($students as $student)
                        <option
                            value="{{ $student->id }}"
                            @selected(old('student_id') == $student->id)
                        >
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('student_id')" class="mt-1" />
            </div>

            <div>
                <label
                    for="teacher_id"
                    class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                >
                    Teacher
                </label>
                <select
                    id="teacher_id"
                    name="teacher_id"
                    class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                           bg-surface-base dark:bg-surface-inverse
                           text-sm text-text dark:text-text-inverse
                           shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                >
                    <option value="">
                        Select a teacher
                    </option>
                    @foreach ($teachers as $teacher)
                        <option
                            value="{{ $teacher->id }}"
                            @selected(old('teacher_id') == $teacher->id)
                        >
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('teacher_id')" class="mt-1" />
            </div>

            <div>
                <label
                    for="subject_id"
                    class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                >
                    Subject
                </label>
                <select
                    id="subject_id"
                    name="subject_id"
                    class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                           bg-surface-base dark:bg-surface-inverse
                           text-sm text-text dark:text-text-inverse
                           shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                >
                    <option value="">
                        Select a subject
                    </option>
                    @foreach ($subjects as $subject)
                        <option
                            value="{{ $subject->id }}"
                            @selected(old('subject_id') == $subject->id)
                        >
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('subject_id')" class="mt-1" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label
                        for="hourly_rate"
                        class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                    >
                        Hourly rate (BRL)
                    </label>
                    <input
                        id="hourly_rate"
                        type="number"
                        step="0.01"
                        name="hourly_rate"
                        value="{{ old('hourly_rate') }}"
                        class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                               bg-surface-base dark:bg-surface-inverse
                               text-sm text-text dark:text-text-inverse
                               shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                    />
                    <x-input-error :messages="$errors->get('hourly_rate')" class="mt-1" />
                </div>

                <div>
                    <label
                        for="status"
                        class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                    >
                        Status
                    </label>
                    <input
                        id="status"
                        type="text"
                        name="status"
                        value="{{ old('status', 'active') }}"
                        class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                               bg-surface-base dark:bg-surface-inverse
                               text-sm text-text dark:text-text-inverse
                               shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                    />
                    <x-input-error :messages="$errors->get('status')" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label
                        for="started_at"
                        class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                    >
                        Starts at
                    </label>
                    <input
                        id="started_at"
                        type="date"
                        name="started_at"
                        value="{{ old('started_at') }}"
                        class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                               bg-surface-base dark:bg-surface-inverse
                               text-sm text-text dark:text-text-inverse
                               shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                    />
                    <x-input-error :messages="$errors->get('started_at')" class="mt-1" />
                </div>

                <div>
                    <label
                        for="ended_at"
                        class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                    >
                        Ends at
                    </label>
                    <input
                        id="ended_at"
                        type="date"
                        name="ended_at"
                        value="{{ old('ended_at') }}"
                        class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                               bg-surface-base dark:bg-surface-inverse
                               text-sm text-text dark:text-text-inverse
                               shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                    />
                    <x-input-error :messages="$errors->get('ended_at')" class="mt-1" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
                <x-button
                    as="a"
                    href="{{ route('mentorships.index') }}"
                    variant="ghost"
                    size="sm"
                >
                    Cancel
                </x-button>

                <x-button
                    type="submit"
                    variant="primary"
                    size="sm"
                >
                    Create mentorship
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
