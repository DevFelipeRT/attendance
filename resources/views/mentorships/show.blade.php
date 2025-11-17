{{-- resources/views/mentorships/show.blade.php --}}
{{-- Mentorship details with balance overview and embedded sessions list. --}}

@php
    /** @var \App\Models\Mentorship\Mentorship $mentorship */
    /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $sessions */
    /** @var array{credits_hours:int,debits_hours:int,balance_hours:int}|null $balance */

    $creditsHours = (int) ($balance['credits_hours'] ?? 0);
    $debitsHours  = (int) ($balance['debits_hours'] ?? 0);
    $balanceHours = (int) ($balance['balance_hours'] ?? 0);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-text dark:text-text-inverse leading-tight">
            Mentorship · {{ $mentorship->student->name ?? 'Mentorship' }}
        </h1>
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
            href="{{ route('mentorships.index') }}"
            variant="ghost"
            size="sm"
            :responsive=false
        >
            Back to list
        </x-button>

        {{-- Mentorship summary --}}
        <div
            class="bg-surface-base dark:bg-surface-inverse
                   rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse
                   p-6"
        >
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Student
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse">
                            {{ $mentorship->student->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Teacher
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse">
                            {{ $mentorship->teacher->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Subject
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse">
                            {{ $mentorship->subject->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Hourly rate
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse tabular-nums">
                            R$
                            {{ number_format((float) $mentorship->hourly_rate, 2, ',', '.') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Status
                        </dt>
                        <dd class="text-sm">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        bg-background-subtle dark:bg-surface-subtle/20
                                        text-text-muted dark:text-text-inverse-muted
                                        capitalize"
                            >
                                {{ $mentorship->status ?? 'active' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Period
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse">
                            {{ optional($mentorship->started_at)?->format('d/m/Y') ?? '—' }}
                            —
                            {{ optional($mentorship->ended_at)?->format('d/m/Y') ?? '—' }}
                        </dd>
                    </div>
                </dl>

                <div class="flex flex-col sm:flex-row flex-wrap justify-end items-center gap-3">
                    <x-button
                        as="a"
                        href="{{ route('mentorships.edit', $mentorship) }}"
                        variant="default"
                        size="sm"
                    >
                        Edit mentorship
                    </x-button>

                    <x-button
                        as="a"
                        href="{{ route('mentorships.payments.index', $mentorship) }}"
                        variant="primary"
                        size="sm"
                    >
                        Payments
                    </x-button>
                </div>
            </div>
        </div>

        {{-- Balance overview (embedded in mentorship details) --}}
        <section aria-labelledby="mentorship-balance-heading">
            <div class="flex items-center justify-between mb-3">
                <h2
                    id="mentorship-balance-heading"
                    class="text-sm font-semibold text-text-subtle dark:text-text-inverse-subtle"
                >
                    Balance overview
                </h2>
            </div>

            <div
                class="bg-surface-base dark:bg-surface-inverse
                       rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse
                       px-4 py-4"
            >
                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-6 rounded-2xl
                           px-4 py-4"
                >
                    <div class="flex justify-center lg:justify-start">
                        <div class="flex flex-col items-center">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                                Credited hours
                            </dt>
                            <dd class="mt-1 text-sm font-semibold text-text dark:text-text-inverse tabular-nums">
                                {{ number_format($creditsHours, 0, ',', '.') }}
                            </dd>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="flex flex-col items-center">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                                Debited hours
                            </dt>
                            <dd class="mt-1 text-sm font-semibold text-text dark:text-text-inverse tabular-nums">
                                {{ number_format($debitsHours, 0, ',', '.') }}
                            </dd>
                        </div>
                    </div>

                    <div class="flex justify-center lg:justify-end">
                        <div class="flex flex-col items-center">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                                Remaining hours
                            </dt>
                            <dd class="mt-1 text-sm font-semibold text-text dark:text-text-inverse tabular-nums">
                                {{ number_format($balanceHours, 0, ',', '.') }}
                            </dd>
                        </div>
                    </div>
                </div>

                <p class="mt-2 text-xs text-text-subtle dark:text-text-inverse-subtle">
                    The balance is computed from mentorship payments (credits) and debits generated from attended or chargeable sessions.
                    Sessions marked as cancelled never debit hours; absences with notice do not debit.
                </p>
            </div>
        </section>

        {{-- Mentorship sessions: embedded index list --}}
        <section class="space-y-3" aria-labelledby="mentorship-sessions-heading">
            <div class="flex gap-3 items-end justify-between">
                <h2
                    id="mentorship-sessions-heading"
                    class="text-sm font-semibold text-text-subtle dark:text-text-inverse-subtle"
                >
                    Sessions
                </h2>

                <x-button
                    as="a"
                    href="{{ route('mentorships.sessions.create', $mentorship) }}"
                    variant="primary"
                    size="sm"
                    :responsive=false
                    class="mb-1"
                >
                    New session
                </x-button>
            </div>

            <div
                class="bg-surface-base dark:bg-surface-inverse
                       rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse"
            >
                <div class="overflow-x-auto rounded-2xl">
                    <table class="w-full table-auto divide-y divide-border-subtle dark:divide-border-inverse">
                        <thead class="bg-surface-alt dark:bg-surface-inverse-alt">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide whitespace-nowrap"
                                >
                                    Date
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide whitespace-nowrap"
                                >
                                    Start
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide whitespace-nowrap"
                                >
                                    Duration
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide whitespace-nowrap"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-2 py-2 text-right text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide whitespace-nowrap w-0"
                                >
                                    <span class="sr-only">Open</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-border-subtle dark:divide-border-inverse
                                   bg-surface-base dark:bg-surface-inverse"
                        >
                            @forelse ($sessions as $item)
                                @php
                                    $rawStartTime = $item->start_time;
                                    $startTimeFormatted = null;

                                    if ($rawStartTime instanceof \DateTimeInterface) {
                                        $startTimeFormatted = $rawStartTime->format('H:i');
                                    } elseif (is_string($rawStartTime) && strlen($rawStartTime) >= 5) {
                                        $startTimeFormatted = substr($rawStartTime, 0, 5);
                                    }
                                @endphp

                                <tr
                                    class="group cursor-pointer transition-colors duration-150
                                           hover:bg-surface-alt/30 dark:hover:bg-surface-inverse-alt"
                                    onclick="window.location='{{ route('mentorships.sessions.attendance.edit', [$mentorship, $item]) }}'"
                                >
                                    <td class="px-3 py-2 text-sm text-text dark:text-text-inverse whitespace-nowrap">
                                        {{ optional($item->session_date)?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-text dark:text-text-inverse tabular-nums whitespace-nowrap">
                                        {{ $startTimeFormatted ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-text dark:text-text-inverse tabular-nums whitespace-nowrap">
                                        @if ($item->duration_minutes !== null)
                                            {{ $item->duration_minutes }} min
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                       bg-background-subtle dark:bg-surface-subtle/20
                                                       text-text-muted dark:text-text-inverse-muted
                                                       capitalize"
                                        >
                                            {{ $item->status?->value ?? 'scheduled' }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-sm text-right align-top w-0 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <svg
                                                class="inline-block h-4 w-4 text-text-muted dark:text-text-inverse-muted
                                                       group-hover:text-text dark:group-hover:text-action-primary-fg
                                                       transition-colors duration-150"
                                                viewBox="0 0 20 20"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M7.5 5L12.5 10L7.5 15"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>

                                            <div class="lg:hidden" onclick="event.stopPropagation()">
                                                <x-action-menu
                                                    showOn=""
                                                    triggerAriaLabel="Open session actions"
                                                    width="w-40"
                                                    :flipGuard="200"
                                                    :items="[
                                                        [
                                                            'type'  => 'link',
                                                            'label' => 'Edit session',
                                                            'href'  => route('mentorships.sessions.edit', [$mentorship, $item]),
                                                        ],
                                                        [
                                                            'type'    => 'form',
                                                            'label'   => 'Cancel session',
                                                            'action'  => route('mentorships.sessions.cancel', [$mentorship, $item]),
                                                            'method'  => 'POST',
                                                            'confirm' => 'Cancelling this session will also delete the attendance. are you sure?',
                                                        ],
                                                    ]"
                                                />
                                            </div>

                                            <div
                                                class="hidden lg:inline-flex items-center gap-2"
                                                onclick="event.stopPropagation()"
                                            >
                                                <x-button
                                                    as="a"
                                                    href="{{ route('mentorships.sessions.edit', [$mentorship, $item]) }}"
                                                    variant="outline"
                                                    size="xs"
                                                >
                                                    Edit
                                                </x-button>

                                                <form
                                                    action="{{ route('mentorships.sessions.cancel', [$mentorship, $item]) }}"
                                                    method="POST"
                                                    onsubmit="event.stopPropagation(); return confirm('Cancelling this session will also delete the attendance. Are you sure?');"
                                                >
                                                    @csrf

                                                    <x-button
                                                        type="submit"
                                                        variant="danger"
                                                        size="xs"
                                                    >
                                                        Cancel
                                                    </x-button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-4 py-6 text-center text-sm text-text-muted dark:text-text-inverse-muted"
                                    >
                                        No sessions created yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
