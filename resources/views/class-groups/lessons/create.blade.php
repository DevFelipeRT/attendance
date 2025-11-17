@php
    use App\Enums\ClassLessonStatus;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            New lesson – {{ $classGroup->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <div
                class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                       border border-border-subtle dark:border-border-inverse"
            >
                <div
                    class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                           bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
                >
                    <h3 class="text-lg font-medium text-text dark:text-text-inverse">
                        Create lesson
                    </h3>

                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        Define the date, time, duration and status for this lesson.
                    </p>

                    <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                        Lessons are tied to the class group schedule; they can be marked as
                        scheduled, completed, cancelled or other states defined by the enum.
                    </p>

                    <p class="mt-2 text-xs text-text-subtle dark:text-text-inverse-subtle">
                        Class group:
                        <span class="font-medium text-text dark:text-text-inverse">
                            {{ $classGroup->name }}
                        </span>
                        @if ($classGroup->subject)
                            • {{ $classGroup->subject->name }}
                        @endif
                    </p>
                </div>

                <div class="px-6 py-6">
                    <form
                        method="POST"
                        action="{{ route('class-groups.lessons.store', $classGroup) }}"
                        class="space-y-6"
                    >
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="lesson_date" value="Lesson date" />
                                <x-text-input
                                    id="lesson_date"
                                    name="lesson_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :value="old('lesson_date')"
                                    required
                                />
                                <x-input-error :messages="$errors->get('lesson_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="start_time" value="Start time" />
                                <x-text-input
                                    id="start_time"
                                    name="start_time"
                                    type="time"
                                    class="mt-1 block w-full"
                                    :value="old('start_time')"
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
                                    min="0"
                                    step="1"
                                    class="mt-1 block w-full"
                                    :value="old('duration_minutes', 60)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                                <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                                    Use the exact duration in minutes (for example, 60, 90, 120).
                                </p>
                            </div>

                            <div>
                                <x-input-label for="status" value="Status" />

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
                                    @php
                                        $defaultStatus = old('status', ClassLessonStatus::Scheduled->value);
                                    @endphp

                                    @foreach (ClassLessonStatus::cases() as $status)
                                        <option
                                            value="{{ $status->value }}"
                                            @selected($defaultStatus === $status->value)
                                        >
                                            {{ ucfirst($status->value) }}
                                        </option>
                                    @endforeach
                                </select>

                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="notes" value="Notes (optional)" />
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                class="mt-1 block w-full rounded-2xl border border-border-subtle dark:border-border-inverse
                                       bg-surface-base dark:bg-surface-inverse text-sm text-text dark:text-text-inverse
                                       focus:border-action-primary-bg focus:ring-2 focus:ring-action-primary-ring
                                       focus:ring-offset-2 focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                       transition ease-in-out duration-150"
                            >{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
                            <x-button
                                as="a"
                                href="{{ route('class-groups.show', $classGroup) }}"
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
                                Save
                            </x-button>
                        </div>
                    </form>

                    @if ($errors->any())
                        <div
                            class="mt-6 rounded-xl border border-status-error-border bg-status-error-softBg px-4 py-3 text-xs
                                   text-status-error-subtleFg"
                        >
                            There are validation errors in the form. Please review the fields.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
