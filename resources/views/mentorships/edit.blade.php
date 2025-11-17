{{-- Mentorships: edit form --}}
{{-- Updates a mentorship contract. --}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Edit mentorship
        </h1>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                   border border-border-subtle dark:border-border-inverse"
        >
            {{-- Header / context --}}
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                       flex flex-col gap-3 md:flex-row md:items-center md:justify-between
                       bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
            >
                <div>
                    <h3 class="text-lg font-medium text-text dark:text-text-inverse">
                        {{ $mentorship->student->name ?? 'Mentorship' }}
                    </h3>
                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        Edit the main configuration of this mentorship contract.
                    </p>
                </div>
            </div>

            <div class="px-6 py-6">
                @if (session('status'))
                    <div
                        class="mb-4 rounded-2xl border border-status-success-border
                               bg-status-success-softBg px-4 py-3 text-sm text-status-success-subtleFg"
                    >
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mb-4 rounded-2xl border border-status-error-border
                               bg-status-error-softBg px-4 py-3 text-sm text-status-error-subtleFg"
                    >
                        Please fix the highlighted fields and try again.
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('mentorships.update', $mentorship) }}"
                    class="space-y-6"
                >
                    @csrf
                    @method('PUT')

                    {{-- Participants and subject --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                                       shadow-sm focus:border-action-primary-bg
                                       focus:ring-2 focus:ring-action-primary-ring
                                       focus:ring-offset-2
                                       focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                       transition ease-in-out duration-150"
                            >
                                @foreach ($students as $student)
                                    <option
                                        value="{{ $student->id }}"
                                        @selected(old('student_id', $mentorship->student_id) == $student->id)
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
                                       shadow-sm focus:border-action-primary-bg
                                       focus:ring-2 focus:ring-action-primary-ring
                                       focus:ring-offset-2
                                       focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                       transition ease-in-out duration-150"
                            >
                                @foreach ($teachers as $teacher)
                                    <option
                                        value="{{ $teacher->id }}"
                                        @selected(old('teacher_id', $mentorship->teacher_id) == $teacher->id)
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
                                       shadow-sm focus:border-action-primary-bg
                                       focus:ring-2 focus:ring-action-primary-ring
                                       focus:ring-offset-2
                                       focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                       transition ease-in-out duration-150"
                            >
                                @foreach ($subjects as $subject)
                                    <option
                                        value="{{ $subject->id }}"
                                        @selected(old('subject_id', $mentorship->subject_id) == $subject->id)
                                    >
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('subject_id')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Financials and period --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                                value="{{ old('hourly_rate', $mentorship->hourly_rate) }}"
                                class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                                       bg-surface-base dark:bg-surface-inverse
                                       text-sm text-text dark:text-text-inverse
                                       shadow-sm focus:border-action-primary-bg
                                       focus:ring-2 focus:ring-action-primary-ring
                                       focus:ring-offset-2
                                       focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                       transition ease-in-out duration-150"
                            />
                            <x-input-error :messages="$errors->get('hourly_rate')" class="mt-1" />
                        </div>

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
                                value="{{ old('started_at', optional($mentorship->started_at)?->format('Y-m-d')) }}"
                                class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                                       bg-surface-base dark:bg-surface-inverse
                                       text-sm text-text dark:text-text-inverse
                                       shadow-sm focus:border-action-primary-bg
                                       focus:ring-2 focus:ring-action-primary-ring
                                       focus:ring-offset-2
                                       focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                       transition ease-in-out duration-150"
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
                                value="{{ old('ended_at', optional($mentorship->ended_at)?->format('Y-m-d')) }}"
                                class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                                       bg-surface-base dark:bg-surface-inverse
                                       text-sm text-text dark:text-text-inverse
                                       shadow-sm focus:border-action-primary-bg
                                       focus:ring-2 focus:ring-action-primary-ring
                                       focus:ring-offset-2
                                       focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                       transition ease-in-out duration-150"
                            />
                            <x-input-error :messages="$errors->get('ended_at')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:max-w-xs">
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
                                value="{{ old('status', $mentorship->status ?? 'active') }}"
                                class="block w-full rounded-2xl border-border-subtle dark:border-border-inverse
                                       bg-surface-base dark:bg-surface-inverse
                                       text-sm text-text dark:text-text-inverse
                                       shadow-sm focus:border-action-primary-bg
                                       focus:ring-2 focus:ring-action-primary-ring
                                       focus:ring-offset-2
                                       focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse
                                       transition ease-in-out duration-150"
                            />
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
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
                            Save changes
                        </x-button>
                    </div>
                </form>

                <hr class="my-6 border-border-subtle dark:border-border-inverse">

                <form
                    method="POST"
                    action="{{ route('mentorships.destroy', $mentorship) }}"
                    onsubmit="return confirm('Delete this mentorship? This action cannot be undone.');"
                >
                    @csrf
                    @method('DELETE')

                    <x-button
                        type="submit"
                        variant="danger"
                        size="sm"
                    >
                        Delete mentorship
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
