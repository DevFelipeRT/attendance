{{-- resources/views/mentorships/attendance/edit.blade.php --}}

@php
    use App\Enums\AttendanceStatus;
    use Carbon\Carbon;

    /**
     * Mentorship session attendance (class-group-like layout).
     *
     * @var \App\Models\Mentorship\Mentorship                $mentorship
     * @var \App\Models\Mentorship\MentorshipSession         $session
     * @var \App\Models\Mentorship\MentorshipAttendance|null $attendance
     */

    $student  = $mentorship->student;
    $subject  = $mentorship->subject;
    $teacher  = $mentorship->teacher;

    $sessionDate = $session->session_date instanceof Carbon
        ? $session->session_date->format('d/m/Y')
        : (string) $session->session_date;

    $sessionTime = $session->start_time instanceof Carbon
        ? $session->start_time->format('H:i')
        : (string) $session->start_time;

    // Controller expects flat fields: 'status' and 'absence_notified'
    $currentStatus   = old('status', $attendance?->status?->value ?? AttendanceStatus::Present->value);
    $absenceNotified = (bool) old('absence_notified', $attendance->absence_notified ?? false);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Attendance – {{ $sessionDate }} – {{ $student->name ?? 'Mentorship' }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div
                class="rounded-2xl border border-status-success-border
                    bg-status-success-softBg px-4 py-3 text-sm
                    text-status-success-subtleFg"
            >
                {{ session('status') }}
            </div>
        @endif

        <x-button
            as="a"
            href="{{ route('mentorships.sessions.index', $mentorship) }}"
            variant="ghost"
            size="sm"
            :responsive=false
        >
            Back to sessions
        </x-button>
        {{-- Session summary --}}
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
                        Mentorship session
                    </h3>
                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        {{ $student->name ?? '—' }}
                        @if ($subject) • {{ $subject->name }} @endif
                        @if ($teacher) • {{ $teacher->name }} @endif
                    </p>
                </div>

                <div class="text-sm text-text-muted dark:text-text-inverse-muted">
                    <div>
                        <span class="font-semibold text-text dark:text-text-inverse">Date:</span>
                        {{ $sessionDate }}
                    </div>
                    <div>
                        <span class="font-semibold text-text dark:text-text-inverse">Time:</span>
                        {{ $sessionTime ?: '—' }}
                    </div>
                </div>
            </div>

            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Session duration
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            @if ($session->duration_minutes)
                                {{ $session->duration_minutes }} minutes
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
                    border border-border-subtle dark:border-border-inverse overflow-hidden"
        >
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                        flex flex-col gap-3 md:flex-row md:items-center md:justify-between
                        bg-surface-alt dark:bg-surface-inverse-alt"
            >
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

            <form
                method="POST"
                action="{{ route('mentorships.sessions.attendance.update', [$mentorship, $session]) }}"
            >
                @csrf
                @method('PUT')

                <div class="px-6 py-6 space-y-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border-strong dark:divide-border-inverse-strong">
                            <thead class="bg-background-muted dark:bg-background-inverse-muted">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium
                                                text-text-subtle dark:text-text-inverse-subtle
                                                uppercase tracking-wider rounded-tl-xl"
                                    >
                                        Student
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium
                                                text-text-subtle dark:text-text-inverse-subtle
                                                uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium
                                                text-text-subtle dark:text-text-inverse-subtle
                                                uppercase tracking-wider rounded-tr-xl"
                                    >
                                        Absence notified
                                    </th>
                                </tr>
                            </thead>

                            <tbody
                                class="divide-y divide-border-subtle dark:divide-border-inverse
                                        bg-surface-base dark:bg-surface-inverse"
                            >
                                <tr x-data="{ status: '{{ $currentStatus }}' }">
                                    {{-- Student --}}
                                    <td class="px-4 py-3 text-sm text-text dark:text-text-inverse align-top">
                                        <div class="space-y-1">
                                            <div class="font-medium">
                                                {{ $student->name ?? '—' }}
                                            </div>
                                            <div class="text-xs text-text-subtle dark:text-text-inverse-subtle">
                                                Session #{{ $session->id }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Status radios (flat "status" name) --}}
                                    <td class="px-4 py-3 text-sm text-text dark:text-text-inverse align-top">
                                        <div class="flex flex-wrap items-center gap-4">
                                            @foreach (AttendanceStatus::cases() as $statusCase)
                                                @php
                                                    $isPresent = $statusCase === AttendanceStatus::Present;
                                                    $isLate    = $statusCase === AttendanceStatus::Late;
                                                    $isAbsent  = $statusCase === AttendanceStatus::Absent;

                                                    $base = 'inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium cursor-pointer transition-colors duration-150';

                                                    $selected = $isPresent
                                                        ? 'border-status-success-border bg-status-success-softBg text-status-success-subtleFg'
                                                        : ($isLate
                                                            ? 'border-status-warning-border bg-status-warning-softBg text-status-warning-subtleFg'
                                                            : 'border-status-error-border bg-status-error-softBg text-status-error-subtleFg');

                                                    $unselected = 'border-border-subtle dark:border-border-inverse bg-transparent text-text-subtle dark:text-text-inverse-subtle';

                                                    $dot = $isPresent
                                                        ? 'bg-status-success-bg'
                                                        : ($isLate
                                                            ? 'bg-status-warning-bg'
                                                            : 'bg-status-error-bg');
                                                @endphp

                                                <label
                                                    class="{{ $base }}"
                                                    x-bind:class="status === '{{ $statusCase->value }}' ? '{{ $selected }}' : '{{ $unselected }}'"
                                                >
                                                    <input
                                                        type="radio"
                                                        name="status"
                                                        value="{{ $statusCase->value }}"
                                                        class="sr-only"
                                                        x-model="status"
                                                    >
                                                    <span class="inline-block h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
                                                    <span>{{ ucfirst($statusCase->value) }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                    </td>

                                    {{-- Absence notified (flat "absence_notified" name) --}}
                                    <td class="px-4 py-3 text-sm text-text dark:text-text-inverse align-top">
                                        <div
                                            class="flex items-center gap-2"
                                            x-bind:class="status !== '{{ AttendanceStatus::Absent->value }}' ? 'opacity-60' : ''"
                                        >
                                            {{-- Hidden default to ensure boolean --}}
                                            <input type="hidden" name="absence_notified" value="0">

                                            <input
                                                type="checkbox"
                                                id="absence_notified"
                                                name="absence_notified"
                                                value="1"
                                                class="rounded border-border-subtle dark:border-border-inverse
                                                        text-primary-400 focus:ring-action-primary-ring
                                                        focus:ring-offset-2 focus:ring-offset-background-muted
                                                        dark:focus:ring-offset-background-inverse"
                                                x-bind:disabled="status !== '{{ AttendanceStatus::Absent->value }}'"
                                                @checked($absenceNotified)
                                            >
                                            <label
                                                for="absence_notified"
                                                class="text-sm text-text-muted dark:text-text-inverse-muted select-none"
                                            >
                                                Notified absence
                                            </label>
                                        </div>

                                        <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                                            Relevant only when the student is marked as absent.
                                        </p>

                                        <x-input-error :messages="$errors->get('absence_notified')" class="mt-2" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if ($errors->any())
                        <div
                            class="mt-4 rounded-xl border border-status-error-border
                                    bg-status-error-softBg px-4 py-3 text-xs
                                    text-status-error-subtleFg"
                        >
                            There are validation errors in the attendance form. Please review the fields highlighted above.
                        </div>
                    @endif

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <x-button
                            type="submit"
                            variant="primary"
                            size="sm"
                        >
                            Save attendance
                        </x-button>
                    </div>
                </div>
            </form>
        </div>

        <p class="text-xs text-text-subtle dark:text-text-inverse-subtle">
            Debits are generated only for chargeable cases and when the session duration is a whole number of hours.
        </p>
    </div>
</x-app-layout>
