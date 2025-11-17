{{-- Mentorship sessions: edit form --}}
{{-- Updates a mentorship session with delete action. --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Edit session – {{ $mentorship->student->name ?? 'Mentorship' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                       border border-border-subtle dark:border-border-inverse"
            >
                <div
                    class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                           bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
                >
                    <h3 class="text-lg font-medium text-text dark:text-text-inverse">
                        Update session
                    </h3>

                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        Adjust the session date, time, duration or status when required.
                    </p>

                    <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                        Mentorship:
                        <span class="font-medium text-text dark:text-text-inverse">
                            {{ $mentorship->student->name ?? '—' }}
                        </span>
                        @if ($mentorship->subject)
                            • {{ $mentorship->subject->name }}
                        @endif
                        @if ($mentorship->teacher)
                            • {{ $mentorship->teacher->name }}
                        @endif
                    </p>
                </div>

                <div class="px-6 py-6">
                    @if (session('status'))
                        <div
                            class="mb-4 rounded-2xl border border-status-success-border
                                   bg-status-success-softBg px-4 py-3 text-sm
                                   text-status-success-subtleFg"
                        >
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div
                            class="mb-4 rounded-2xl border border-status-error-border
                                   bg-status-error-softBg px-4 py-3 text-sm
                                   text-status-error-subtleFg"
                        >
                            Please fix the errors and try again.
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('mentorships.sessions.update', [$mentorship, $session]) }}"
                        class="space-y-6"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="session_date" value="Date" />
                                <x-text-input
                                    id="session_date"
                                    name="session_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :value="old('session_date', optional($session->session_date)?->format('Y-m-d'))"
                                    required
                                />
                                <x-input-error :messages="$errors->get('session_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="start_time" value="Start time" />
                                <x-text-input
                                    id="start_time"
                                    name="start_time"
                                    type="time"
                                    class="mt-1 block w-full"
                                    :value="old('start_time', $session->start_time)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="duration_minutes" value="Duration (minutes)" />
                                <x-text-input
                                    id="duration_minutes"
                                    name="duration_minutes"
                                    type="number"
                                    min="60"
                                    step="60"
                                    class="mt-1 block w-full"
                                    :value="old('duration_minutes', $session->duration_minutes)"
                                    required
                                />
                                <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                                    Must be a multiple of 60.
                                </p>
                                <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" value="Status" />

                                @php
                                    $currentStatus = old('status', $session->status?->value ?? 'scheduled');
                                @endphp

                                <select
                                    id="status"
                                    name="status"
                                    class="mt-1 block w-full rounded-2xl border border-border-subtle dark:border-border-inverse
                                           bg-surface-base dark:bg-surface-inverse text-sm text-text dark:text-text-inverse
                                           focus:border-action-primary-bg focus:ring-2 focus:ring-action-primary-ring
                                           focus:ring-offset-2 focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                           transition ease-in-out duration-150"
                                    required
                                >
                                    <option value="scheduled" @selected($currentStatus === 'scheduled')>
                                        Scheduled
                                    </option>
                                    <option value="completed" @selected($currentStatus === 'completed')>
                                        Completed
                                    </option>
                                </select>

                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <x-button
                                as="a"
                                href="{{ route('mentorships.show', $mentorship) }}"
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
                                Update
                            </x-button>
                        </div>
                    </form>

                    <hr class="my-6 border-border-subtle dark:border-border-inverse">

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                        <div class="flex flex-col sm:flex-row w-full sm:w-auto items-center justify-start gap-3">
                            <form
                                action="{{ route('mentorships.sessions.cancel', [$mentorship, $session]) }}"
                                method="POST"
                                onsubmit="event.stopPropagation(); return confirm('Cancelling this session will also delete the attendance. Are you sure?');"
                                class="w-full sm:w-auto"
                            >
                                @csrf

                                <x-button
                                    type="submit"
                                    variant="danger"
                                    size="sm"
                                >
                                    Cancel session
                                </x-button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('mentorships.sessions.destroy', [$mentorship, $session]) }}"
                                onsubmit="return confirm('Delete this session? This action cannot be undone and may affect mentorship balance.');"
                                class="w-full sm:w-auto"
                            >
                                @csrf
                                @method('DELETE')

                                <x-button
                                    type="submit"
                                    variant="danger"
                                    size="sm"
                                >
                                    Delete session
                                </x-button>
                            </form>
                        </div>

                        <p class="text-xs text-text-subtle dark:text-text-inverse-subtle">
                            Deleting the session removes it from the mentorship schedule and related reports.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
