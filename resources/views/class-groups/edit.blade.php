<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Edit class group
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                    border border-border-subtle dark:border-border-inverse"
        >
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                        flex flex-col gap-3 md:flex-row md:items-center md:justify-between
                        bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
            >
                <div>
                    <h3 class="text-lg font-medium text-text dark:text-text-inverse">
                        {{ $classGroup->name }}
                    </h3>
                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        Edit the main configuration of this class group.
                    </p>
                </div>
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
                        Please fix the highlighted fields and try again.
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('class-groups.update', $classGroup) }}"
                    class="space-y-6"
                >
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            class="mt-1"
                            :value="old('name', $classGroup->name)"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="subject_id" value="Subject" />
                            <select
                                id="subject_id"
                                name="subject_id"
                                class="mt-1 block w-full rounded-2xl
                                        border border-border-subtle dark:border-border-inverse
                                        bg-surface-base dark:bg-surface-inverse
                                        text-sm text-text dark:text-text-inverse
                                        focus:border-action-primary-bg
                                        focus:ring-2 focus:ring-action-primary-ring
                                        focus:ring-offset-2
                                        focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                        transition ease-in-out duration-150"
                            >
                                @foreach ($subjects as $subject)
                                    <option
                                        value="{{ $subject->id }}"
                                        @selected(old('subject_id', $classGroup->subject_id) == $subject->id)
                                    >
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('subject_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="teacher_id" value="Teacher" />
                            <select
                                id="teacher_id"
                                name="teacher_id"
                                class="mt-1 block w-full rounded-2xl
                                        border border-border-subtle dark:border-border-inverse
                                        bg-surface-base dark:bg-surface-inverse
                                        text-sm text-text dark:text-text-inverse
                                        focus:border-action-primary-bg
                                        focus:ring-2 focus:ring-action-primary-ring
                                        focus:ring-offset-2
                                        focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                        transition ease-in-out duration-150"
                            >
                                @foreach ($teachers as $teacher)
                                    <option
                                        value="{{ $teacher->id }}"
                                        @selected(old('teacher_id', $classGroup->teacher_id) == $teacher->id)
                                    >
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="term_start_date" value="Term start date" />
                            <x-text-input
                                id="term_start_date"
                                name="term_start_date"
                                type="date"
                                class="mt-1"
                                :value="old('term_start_date', optional($classGroup->term_start_date)?->format('Y-m-d'))"
                            />
                            <x-input-error :messages="$errors->get('term_start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="term_end_date" value="Term end date" />
                            <x-text-input
                                id="term_end_date"
                                name="term_end_date"
                                type="date"
                                class="mt-1"
                                :value="old('term_end_date', optional($classGroup->term_end_date)?->format('Y-m-d'))"
                            />
                            <x-input-error :messages="$errors->get('term_end_date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label
                                for="default_lesson_duration_minutes"
                                value="Default lesson duration (minutes)"
                            />
                            <x-text-input
                                id="default_lesson_duration_minutes"
                                name="default_lesson_duration_minutes"
                                type="number"
                                min="0"
                                step="5"
                                class="mt-1"
                                :value="old('default_lesson_duration_minutes', $classGroup->default_lesson_duration_minutes)"
                            />
                            <x-input-error :messages="$errors->get('default_lesson_duration_minutes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
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
                            Save changes
                        </x-button>
                    </div>
                </form>

                <hr class="my-6 border-border-subtle dark:border-border-inverse">

                <form
                    method="POST"
                    action="{{ route('class-groups.destroy', $classGroup) }}"
                    onsubmit="return confirm('Delete this class group? This will affect related lessons and enrollments.');"
                >
                    @csrf
                    @method('DELETE')

                    <x-button
                        type="submit"
                        variant="danger"
                        size="sm"
                    >
                        Delete class group
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
