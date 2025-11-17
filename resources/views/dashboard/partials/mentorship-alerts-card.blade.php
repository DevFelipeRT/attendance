{{-- resources/views/dashboard/partials/mentorship-alerts-card.blade.php --}}

@php
    /**
     * Expected variables in scope:
     *
     * @var \Illuminate\Support\Collection|null $lowBalanceMentorships
     */
@endphp

<div
    class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
           border border-border-subtle dark:border-border-inverse overflow-hidden"
>
    <div
        class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
               bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
    >
        <h3 class="text-sm font-semibold text-text dark:text-text-inverse">
            Mentorship alerts
        </h3>
        <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
            Mentorships with low remaining hours or requiring attention.
        </p>
    </div>

    <div class="px-6 py-6 space-y-4">
        @if (!isset($lowBalanceMentorships) || $lowBalanceMentorships->isEmpty())
            <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                There are no mentorship alerts at the moment.
            </p>
        @else
            <ul class="space-y-3">
                @foreach ($lowBalanceMentorships as $item)
                    @php
                        /**
                         * Expected structure for each item:
                         * - 'enrollment'    => ClassEnrollment
                         * - 'hours_balance' => float
                         */
                        $enrollment   = $item['enrollment'] ?? null;
                        $balanceHours = isset($item['hours_balance']) ? (float) $item['hours_balance'] : null;
                        $group        = $enrollment?->classGroup;
                        $student      = $enrollment?->student;

                        $balanceTextClass = 'text-status-warning-subtleFg';
                        if ($balanceHours !== null && $balanceHours <= 0.0) {
                            $balanceTextClass = 'text-status-error-subtleFg';
                        }

                        $isAlertClickable = $enrollment !== null;
                    @endphp

                    <li>
                        @if ($isAlertClickable)
                            <a
                                href="{{ route('enrollments.mentorship.balance.show', $enrollment) }}"
                                class="group block rounded-2xl border border-border-subtle dark:border-border-inverse
                                       bg-surface-base dark:bg-surface-inverse px-4 py-3
                                       hover:bg-surface-alt dark:hover:bg-surface-inverse-alt
                                       transition-colors duration-150"
                            >
                        @else
                            <div
                                class="rounded-2xl border border-border-subtle dark:border-border-inverse
                                       bg-surface-base dark:bg-surface-inverse px-4 py-3"
                            >
                        @endif
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p
                                            class="min-w-0 text-sm font-semibold text-text dark:text-text-inverse
                                                   break-words hyphens-auto line-clamp-3"
                                        >
                                            {{ $group?->name ?? 'Mentorship' }}
                                        </p>
                                        <p
                                            class="mt-0.5 min-w-0 text-xs text-text-muted dark:text-text-inverse-muted
                                                   break-words hyphens-auto line-clamp-3"
                                        >
                                            {{ $student?->name ?? 'Student not available' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="text-right">
                                            <div class="text-sm font-semibold {{ $balanceTextClass }}">
                                                @if ($balanceHours !== null)
                                                    {{ number_format($balanceHours, 2, ',', '.') }} h
                                                @else
                                                    —
                                                @endif
                                            </div>
                                            <div class="mt-0.5 text-xs text-text-subtle dark:text-text-inverse-subtle">
                                                Remaining balance
                                            </div>
                                        </div>

                                        @if ($isAlertClickable)
                                            <span
                                                class="inline-flex items-center justify-center rounded-full
                                                       text-text-subtle dark:text-text-inverse-subtle
                                                       group-hover:text-action-primary-fg transition-colors duration-150"
                                                aria-hidden="true"
                                            >
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    class="h-4 w-4 transform group-hover:translate-x-0.5 transition-transform duration-150"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >
                                                    <path d="M9 5l7 7-7 7" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                        @if ($isAlertClickable)
                            </a>
                        @else
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
