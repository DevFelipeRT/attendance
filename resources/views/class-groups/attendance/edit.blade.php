{{-- resources/views/class-groups/attendance/edit.blade.php --}}

@php
    use App\Enums\AttendanceStatus;
    use Carbon\Carbon;

    /** @var \App\Models\ClassLesson $lesson */
    /** @var \App\Models\ClassGroup $classGroup */
    /** @var \Illuminate\Support\Collection<int,array{
        enrollment:\App\Models\ClassEnrollment,
        student:\App\Models\Student,
        attendance:?\App\Models\Attendance
    }> $rows */

    $lessonDate =
        $lesson->lesson_date instanceof Carbon ? $lesson->lesson_date->format('d/m/Y') : (string) $lesson->lesson_date;

    $lessonTime =
        $lesson->start_time instanceof Carbon ? $lesson->start_time->format('H:i') : (string) $lesson->start_time;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Attendance – {{ $lessonDate }} – {{ $classGroup->name }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div
                class="rounded-2xl border border-status-success-border
                       bg-status-success-softBg px-4 py-3 text-sm
                       text-status-success-subtleFg">
                {{ session('status') }}
            </div>
        @endif

        <x-button as="a" href="{{ route('class-groups.show', $classGroup) }}" variant="ghost" size="sm">
            Back to group
        </x-button>
        {{-- Lesson summary --}}
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                    border border-border-subtle dark:border-border-inverse">
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                        flex flex-col gap-3 md:flex-row md:items-center md:justify-between
                        bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-medium text-text dark:text-text-inverse">
                        Class lesson
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

                <div class="text-sm text-text-muted dark:text-text-inverse-muted">
                    <div>
                        <span class="font-semibold text-text dark:text-text-inverse">Date:</span>
                        {{ $lessonDate }}
                    </div>
                    <div>
                        <span class="font-semibold text-text dark:text-text-inverse">Time:</span>
                        {{ $lessonTime ?: '—' }}
                    </div>
                </div>
            </div>

            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt
                            class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Total students
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            {{ $rows->count() }}
                        </dd>
                    </div>

                    <div>
                        <dt
                            class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Default lesson duration
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            @if ($lesson->duration_minutes)
                                {{ $lesson->duration_minutes }} minutes
                            @elseif ($classGroup->default_lesson_duration_minutes)
                                {{ $classGroup->default_lesson_duration_minutes }} minutes
                            @else
                                Not defined
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Attendance form --}}
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                    border border-border-subtle dark:border-border-inverse overflow-hidden">
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                        flex flex-col gap-3 md:flex-row md:items-center md:justify-between
                        bg-surface-alt dark:bg-surface-inverse-alt">
                <h3 class="text-sm font-semibold text-text dark:text-text-inverse">
                    Attendance register
                </h3>

                <div class="flex flex-wrap items-center gap-3 text-xs text-text-subtle dark:text-text-inverse-subtle">
                    <span class="inline-flex items-center gap-1">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-status-success-bg"></span>
                        Present
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-status-warning-bg"></span>
                        Late
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-status-error-bg"></span>
                        Absent
                    </span>
                </div>
            </div>

            <form method="POST"
                action="{{ route('class-groups.lessons.attendance.update', [$classGroup, $lesson]) }}">
                @csrf

                <div class="px-6 py-6 space-y-4">
                    @if ($rows->isEmpty())
                        <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                            There are no students enrolled for this lesson.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full divide-y divide-border-strong
                                        dark:divide-border-inverse-strong">
                                <thead class="bg-background-muted dark:bg-background-inverse-muted">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium
                                                    text-text-subtle dark:text-text-inverse-subtle
                                                    uppercase tracking-wider rounded-tl-xl">
                                            Student
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium
                                                    text-text-subtle dark:text-text-inverse-subtle
                                                    uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-2 text-left text-xs font-medium
                                                    text-text-subtle dark:text-text-inverse-subtle
                                                    uppercase tracking-wider rounded-tr-xl">
                                            Absence notified
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-border-subtle dark:divide-border-inverse
                                            bg-surface-base dark:bg-surface-inverse">
                                    @foreach ($rows as $row)
                                        @php
                                            /** @var \App\Models\ClassEnrollment $enrollment */
                                            $enrollment = $row['enrollment'];
                                            /** @var \App\Models\Student $student */
                                            $student = $row['student'];
                                            /** @var \App\Models\Attendance|null $attendance */
                                            $attendance = $row['attendance'] ?? null;

                                            $key = $enrollment->getKey();

                                            $currentStatus = old(
                                                "rows.$key.status",
                                                $attendance?->status?->value ?? AttendanceStatus::Present->value,
                                            );

                                            $absenceNotified = (bool) old(
                                                "rows.$key.absence_notified",
                                                $attendance?->absence_notified ?? false,
                                            );
                                        @endphp

                                        <tr x-data="{
                                            status: '{{ $currentStatus }}',
                                        }">
                                            {{-- Student --}}
                                            <td class="px-4 py-3 text-sm text-text dark:text-text-inverse align-top">
                                                <div class="space-y-1">
                                                    <div class="font-medium">
                                                        {{ $student->name }}
                                                    </div>
                                                    <div class="text-xs text-text-subtle dark:text-text-inverse-subtle">
                                                        ID: {{ $student->id }}
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Status radios --}}
                                            <td class="px-4 py-3 text-sm text-text dark:text-text-inverse align-top">
                                                <div class="flex flex-wrap items-center gap-4">
                                                    @foreach (AttendanceStatus::cases() as $status)
                                                        @php
                                                            $isPresent = $status === AttendanceStatus::Present;
                                                            $isLate = $status === AttendanceStatus::Late;
                                                            $isAbsent = $status === AttendanceStatus::Absent;

                                                            $baseClasses =
                                                                'inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium cursor-pointer transition-colors duration-150';

                                                            $selectedVariant = $isPresent
                                                                ? 'border-status-success-border bg-status-success-softBg text-status-success-subtleFg'
                                                                : ($isLate
                                                                    ? 'border-status-warning-border bg-status-warning-softBg text-status-warning-subtleFg'
                                                                    : 'border-status-error-border bg-status-error-softBg text-status-error-subtleFg');

                                                            $unselectedVariant =
                                                                'border-border-subtle dark:border-border-inverse bg-transparent text-text-subtle dark:text-text-inverse-subtle';

                                                            $dotClass = $isPresent
                                                                ? 'bg-status-success-bg'
                                                                : ($isLate
                                                                    ? 'bg-status-warning-bg'
                                                                    : 'bg-status-error-bg');
                                                        @endphp

                                                        <label class="{{ $baseClasses }}"
                                                            x-bind:class="status === '{{ $status->value }}'
                                                                ?
                                                                '{{ $selectedVariant }}' :
                                                                '{{ $unselectedVariant }}'">
                                                            <input type="radio"
                                                                name="rows[{{ $key }}][status]"
                                                                value="{{ $status->value }}" class="sr-only"
                                                                x-model="status">
                                                            <span
                                                                class="inline-block h-2.5 w-2.5 rounded-full {{ $dotClass }}"></span>
                                                            <span>{{ ucfirst($status->value) }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <x-input-error :messages="$errors->get("rows.$key.status")" class="mt-2" />
                                            </td>

                                            {{-- Absence notified --}}
                                            <td class="px-4 py-3 text-sm text-text dark:text-text-inverse align-top">
                                                <div class="flex items-center gap-2"
                                                    x-bind:class="status !== '{{ AttendanceStatus::Absent->value }}' ? 'opacity-60' :
                                                        ''">
                                                    {{-- Hidden default value to ensure a boolean is always sent --}}
                                                    <input type="hidden"
                                                        name="rows[{{ $key }}][absence_notified]"
                                                        value="0">

                                                    <input type="checkbox" id="absence_notified_{{ $key }}"
                                                        name="rows[{{ $key }}][absence_notified]"
                                                        value="1"
                                                        class="rounded border-border-subtle dark:border-border-inverse
                                                                text-primary-400 focus:ring-primary-400"
                                                        x-bind:disabled="status !== '{{ AttendanceStatus::Absent->value }}'"
                                                        @checked($absenceNotified)>
                                                    <label for="absence_notified_{{ $key }}"
                                                        class="text-sm text-text-muted dark:text-text-inverse-muted select-none">
                                                        Notified absence
                                                    </label>
                                                </div>
                                                <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                                                    Relevant only when the student is marked as absent.
                                                </p>
                                                <x-input-error :messages="$errors->get("rows.$key.absence_notified")"
                                                    class="mt-2" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if ($errors->any() && $rows->isNotEmpty())
                        @foreach ($errors->all() as $error)
                            <x-alert variant="error">
                                {{ $error }}
                            </x-alert>
                        @endforeach
                    @endif

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <x-button type="submit" variant="primary" size="sm">
                            Save attendance
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
