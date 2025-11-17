{{-- Mentorship sessions: create form --}}
{{-- Creates a new session enforcing whole-hour duration. --}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-text dark:text-text-inverse">
            New session · {{ $mentorship->student->name ?? 'Mentorship' }}
        </h1>
    </x-slot>

    <div class="max-w-2xl mx-auto">
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
            action="{{ route('mentorships.sessions.store', $mentorship) }}"
            class="bg-surface-base dark:bg-surface-inverse
                   rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse
                   p-6 space-y-4"
        >
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-1">
                    <label
                        for="session_date"
                        class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                    >
                        Date
                    </label>
                    <input
                        id="session_date"
                        type="date"
                        name="session_date"
                        value="{{ old('session_date') }}"
                        class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                               bg-surface-base dark:bg-surface-inverse
                               text-sm text-text dark:text-text-inverse
                               shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                    />
                    <x-input-error :messages="$errors->get('session_date')" class="mt-1" />
                </div>

                <div class="sm:col-span-1">
                    <label
                        for="start_time"
                        class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                    >
                        Start time
                    </label>
                    <input
                        id="start_time"
                        type="time"
                        name="start_time"
                        value="{{ old('start_time') }}"
                        class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                               bg-surface-base dark:bg-surface-inverse
                               text-sm text-text dark:text-text-inverse
                               shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                    />
                    <x-input-error :messages="$errors->get('start_time')" class="mt-1" />
                </div>

                <div class="sm:col-span-1">
                    <label
                        for="duration_minutes"
                        class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                    >
                        Duration (minutes)
                    </label>
                    <input
                        id="duration_minutes"
                        type="number"
                        step="60"
                        min="60"
                        name="duration_minutes"
                        value="{{ old('duration_minutes', 60) }}"
                        class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                               bg-surface-base dark:bg-surface-inverse
                               text-sm text-text dark:text-text-inverse
                               shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                    />
                    <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                        Must be a multiple of 60.
                    </p>
                    <x-input-error :messages="$errors->get('duration_minutes')" class="mt-1" />
                </div>
            </div>

            <div>
                <label
                    for="status"
                    class="block text-sm font-medium text-text-muted dark:text-text-inverse-muted mb-1"
                >
                    Status
                </label>
                <select
                    id="status"
                    name="status"
                    class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                           bg-surface-base dark:bg-surface-inverse
                           text-sm text-text dark:text-text-inverse
                           shadow-sm focus:border-action-primary-bg focus:ring-action-primary-ring"
                >
                    <option value="scheduled" @selected(old('status') === 'scheduled')>
                        scheduled
                    </option>
                    <option value="completed" @selected(old('status') === 'completed')>
                        completed
                    </option>
                    <option value="cancelled" @selected(old('status') === 'cancelled')>
                        cancelled
                    </option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-1" />
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
                <x-button
                    as="a"
                    href="{{ route('mentorships.sessions.index', $mentorship) }}"
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
                    Create session
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
