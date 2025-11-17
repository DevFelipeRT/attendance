@php
    use App\Enums\ClassLessonStatus;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Edit lesson – {{ $classGroup->name }}
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
                        Update lesson
                    </h3>

                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        Adjust the lesson date, time, duration or status when required.
                    </p>

                    <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
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
                        action="{{ route('class-groups.lessons.update', [$classGroup, $lesson]) }}"
                        class="space-y-6"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="lesson_date" value="Lesson date" />
                                <x-text-input
                                    id="lesson_date"
                                    name="lesson_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :value="old('lesson_date', optional($lesson->lesson_date)?->format('Y-m-d'))"
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
                                    :value="old('start_time', \Carbon\Carbon::parse($lesson->start_time)->format('H:i'))"
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
                                    :value="old('duration_minutes', $lesson->duration_minutes)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" value="Status" />

                                @php
                                    $currentStatus = old('status', $lesson->status?->value ?? null);
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
                                    @foreach (ClassLessonStatus::cases() as $status)
                                        @if ($status === ClassLessonStatus::Cancelled && $status->value !== $currentStatus)
                                            @continue
                                        @endif
                                        <option
                                            value="{{ $status->value }}"
                                            @selected($currentStatus === $status->value)
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
                            >{{ old('notes', $lesson->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
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
                                Update
                            </x-button>
                        </div>
                    </form>

                    <hr class="my-6 border-border-subtle dark:border-border-inverse">

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                        <div class="flex flex-col sm:flex-row w-full sm:w-fit items-center justify-start gap-3">
                            <form
                                action="{{ route('class-groups.lessons.cancel', [$classGroup, $lesson]) }}"
                                method="POST"
                                onsubmit="event.stopPropagation(); return confirm('Cancelling the lesson will also delete the attendance permanently. Would you like to continue?');"
                                class="w-full sm:w-fit"
                            >
                                @csrf

                                <x-button
                                    type="submit"
                                    variant="danger"
                                    size="sm"
                                >
                                    Cancel lesson
                                </x-button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('class-groups.lessons.destroy', [$classGroup, $lesson]) }}"
                                onsubmit="return confirm('Delete this lesson? This action cannot be undone.');"
                                class="w-full sm:w-fit"
                            >
                                @csrf
                                @method('DELETE')

                                <x-button
                                    type="submit"
                                    variant="danger"
                                    size="sm"
                                >
                                    Delete lesson
                                </x-button>
                            </form>
                        </div>

                        <p class="text-xs text-text-subtle dark:text-text-inverse-subtle">
                            Deleting the lesson removes it from the schedule and from reports that depend on it.
                        </p>
                    </div>

                    @if (session('status'))
                        <div
                            class="mt-6 rounded-xl border border-status-success-border bg-status-success-softBg px-4 py-3 text-xs
                                   text-status-success-subtleFg"
                        >
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
