{{-- resources/views/class-groups/enrollments/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Enrollments – {{ $classGroup->name }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <x-button
            as="a"
            href="{{ route('class-groups.show', $classGroup) }}"
            variant="ghost"
            size="sm"
        >
            Back to class group
        </x-button>
        {{-- Summary / header card --}}
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
                        Class group
                    </h3>
                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        {{ $classGroup->name }}
                        @if ($classGroup->subject)
                            • {{ $classGroup->subject->name }}
                        @endif
                        @if ($classGroup->teacher)
                            • {{ $classGroup->teacher->name }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Total enrollments
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            {{ $enrollments->count() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Period
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            @if ($classGroup->term_start_date || $classGroup->term_end_date)
                                {{ $classGroup->term_start_date?->format('d/m/Y') ?? '—' }} –
                                {{ $classGroup->term_end_date?->format('d/m/Y') ?? '—' }}
                            @else
                                Not defined
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Default lesson duration
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            @if ($classGroup->default_lesson_duration_minutes)
                                {{ $classGroup->default_lesson_duration_minutes }} minutes
                            @else
                                Not defined
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Enrollments + form --}}
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                    border border-border-subtle dark:border-border-inverse overflow-hidden"
        >
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                        flex flex-col gap-3 md:flex-row md:items-center md:justify-between
                        bg-surface-alt dark:bg-surface-inverse-alt"
            >
                <h3 class="text-sm font-semibold text-text dark:text-text-inverse">
                    Enrollments
                </h3>

                @if (session('status'))
                    <div class="rounded-xl border border-status-success-border bg-status-success-softBg px-3 py-2 text-xs text-status-success-subtleFg">
                        {{ session('status') }}
                    </div>
                @endif
            </div>

            <div class="px-6 py-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- List --}}
                <div class="lg:col-span-2">
                    @if ($enrollments->isEmpty())
                        <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                            No students enrolled in this class group.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-border-strong dark:divide-border-inverse-strong">
                                <thead class="bg-background-muted dark:bg-background-inverse-muted">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-text-subtle dark:text-text-inverse-subtle uppercase tracking-wider"
                                        >
                                            Student
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium text-text-subtle dark:text-text-inverse-subtle uppercase tracking-wider"
                                        >
                                            Enrolled at
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-4 py-2 text-right text-xs font-medium text-text-subtle dark:text-text-inverse-subtle uppercase tracking-wider"
                                        >
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border-subtle dark:divide-border-inverse bg-surface-base dark:bg-surface-inverse">
                                    @foreach ($enrollments as $enrollment)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-text dark:text-text-inverse">
                                                {{ $enrollment->student?->name ?? ('#' . $enrollment->student_id) }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-text-muted dark:text-text-inverse-muted">
                                                @if ($enrollment->enrolled_at)
                                                    {{ $enrollment->enrolled_at->format('d/m/Y') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="flex justify-end px-4 py-2 text-sm">
                                                <form
                                                    action="{{ route('class-groups.enrollments.destroy', [$classGroup, $enrollment]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Remove this enrollment?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <x-button
                                                        type="submit"
                                                        variant="danger"
                                                        size="sm"
                                                    >
                                                        Remove
                                                    </x-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Form --}}
                <div>
                    <h4 class="text-sm font-semibold text-text dark:text-text-inverse mb-4">
                        Add enrollment
                    </h4>

                    <form
                        method="POST"
                        action="{{ route('class-groups.enrollments.store', $classGroup) }}"
                        class="space-y-4"
                    >
                        @csrf

                        <div>
                            <x-input-label for="student_id" value="Student" />
                            <select
                                id="student_id"
                                name="student_id"
                                class="mt-1 block w-full rounded-xl border border-border-subtle dark:border-border-inverse
                                        bg-surface-base dark:bg-surface-inverse text-sm text-text dark:text-text-inverse
                                        focus:outline-none focus:ring-2 focus:ring-action-primary-ring"
                                required
                            >
                                <option value="">Select a student</option>
                                @foreach ($students as $student)
                                    <option
                                        value="{{ $student->id }}"
                                        @selected(old('student_id') == $student->id)
                                    >
                                        {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="enrolled_at" value="Enrolled at" />
                            <x-text-input
                                id="enrolled_at"
                                name="enrolled_at"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('enrolled_at')"
                            />
                            <x-input-error :messages="$errors->get('enrolled_at')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-button
                                type="submit"
                                variant="primary"
                                size="sm"
                                :disabled="$students->isEmpty()"
                            >
                                Enroll student
                            </x-button>
                        </div>
                    </form>

                    @if ($errors->any() && (! $errors->has('student_id') || ! $errors->has('enrolled_at')))
                        <div class="mt-4 rounded-xl border border-status-error-border bg-status-error-softBg px-4 py-3 text-xs text-status-error-subtleFg">
                            There are validation errors in the form. Please review the fields.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
