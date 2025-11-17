{{-- resources/views/dashboard/partials/upcoming-sessions-card.blade.php --}}

@php
    /**
     * Expected variables in scope:
     *
     * @var \Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|null $upcomingSessions
     * @var int|null $upcomingSessionsCount
     */
@endphp

<div
    class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
           border border-border-subtle dark:border-border-inverse overflow-hidden"
>
    <div
        class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
               flex flex-col gap-3 md:flex-row md:items-center md:justify-between
               bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
    >
        <div>
            <h3 class="text-sm font-semibold text-text dark:text-text-inverse">
                Upcoming sessions
            </h3>
            <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                Next one-to-one mentorship sessions.
            </p>
        </div>

        <div class="text-sm text-text-muted dark:text-text-inverse-muted">
            @isset($upcomingSessionsCount)
                {{ $upcomingSessionsCount }} total
            @elseif(isset($upcomingSessions) && $upcomingSessions instanceof \Illuminate\Support\Collection)
                {{ $upcomingSessions->count() }} total
            @else
                —
            @endisset
        </div>
    </div>

    <div class="px-6 py-6">
        @if (!isset($upcomingSessions) || $upcomingSessions->isEmpty())
            <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                There are no upcoming sessions registered.
            </p>
        @else
            <div class="overflow-x-auto">
                <table
                    class="w-full table-fixed"
                >
                    <thead class="bg-surface-alt dark:bg-surface-inverse-alt rounded-full">
                        <tr>
                            {{-- Date --}}
                            <th
                                scope="col"
                                class="w-[6.5rem] px-3 py-3 text-left text-xs font-medium
                                       text-text-subtle dark:text-text-inverse-subtle
                                       uppercase tracking-wide rounded-l-full"
                            >
                                Date
                            </th>

                            {{-- Time --}}
                            <th
                                scope="col"
                                class="w-[4.5rem] px-2 py-3 text-center text-xs font-medium
                                       text-text-subtle dark:text-text-inverse-subtle
                                       uppercase tracking-wide"
                            >
                                Time
                            </th>

                            {{-- Status (logo após Time) --}}
                            <th
                                scope="col"
                                class="w-[7rem] px-3 py-3 text-center text-xs font-medium
                                       text-text-subtle dark:text-text-inverse-subtle
                                       uppercase tracking-wide hidden sm:table-cell"
                            >
                                Status
                            </th>

                            {{-- Student (coluna flexível) --}}
                            <th
                                scope="col"
                                class="px-3 py-3 text-left text-xs font-medium
                                       text-text-subtle dark:text-text-inverse-subtle
                                       uppercase tracking-wide"
                            >
                                Student
                            </th>

                            {{-- Ícone --}}
                            <th
                                scope="col"
                                class="w-[2.5rem] px-2 py-3 text-right text-xs font-medium
                                       text-text-subtle dark:text-text-inverse-subtle
                                       uppercase tracking-wide rounded-r-full"
                            >
                                <span class="sr-only">Open</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-border-subtle dark:divide-border-inverse
                               bg-surface-base dark:bg-surface-inverse"
                    >
                        @foreach ($upcomingSessions as $session)
                            @php
                                /** @var \App\Models\Mentorship\MentorshipSession|\App\Models\MentorshipSession $session */
                                $mentorship  = $session->mentorship ?? null;
                                $studentName = $mentorship?->student?->name ?? null;

                                $rawStatus   = $session->status ?? null;
                                $statusLabel = null;

                                if ($rawStatus instanceof UnitEnum) {
                                    $statusLabel = ucfirst($rawStatus->value);
                                } elseif (is_string($rawStatus)) {
                                    $statusLabel = ucfirst($rawStatus);
                                }

                                $isSessionClickable = $mentorship !== null;
                            @endphp

                            <tr
                                @if ($isSessionClickable)
                                    class="group cursor-pointer hover:bg-surface-alt dark:hover:bg-surface-inverse-alt transition-colors duration-150"
                                    onclick="window.location='{{ route('mentorships.show', $mentorship) }}'"
                                @else
                                    class="group rounded-full"
                                @endif
                            >
                                {{-- Date --}}
                                <td class="w-[6.5rem] px-3 py-3 text-sm text-text dark:text-text-inverse whitespace-nowrap align-top">
                                    {{ optional($session->session_date)?->format('d/m/Y') ?? '—' }}
                                </td>

                                {{-- Time --}}
                                <td class="w-[4.5rem] px-2 py-3 text-sm text-text dark:text-text-inverse text-center whitespace-nowrap align-top">
                                    @if ($session->start_time)
                                        {{ $session->start_time }}
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="w-[7rem] px-3 py-3 text-sm text-text-muted dark:text-text-inverse-muted whitespace-nowrap hidden sm:table-cell text-center align-top">
                                    {{ $statusLabel ?? '—' }}
                                </td>

                                {{-- Student (wrap + truncamento) --}}
                                <td class="px-3 py-3 align-top">
                                    <div class="min-w-0">
                                        <p
                                            class="min-w-0 text-sm text-text dark:text-text-inverse
                                                   break-words hyphens-auto line-clamp-3"
                                        >
                                            {{ $studentName ?? '—' }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Ícone --}}
                                <td class="w-[2.5rem] px-2 text-sm text-right align-top">
                                    @if ($isSessionClickable)
                                        <span
                                            class="flex h-full items-center justify-center rounded-r-full
                                                   text-text-subtle dark:text-text-inverse-subtle
                                                   group-hover:text-text dark:group-hover:text-action-primary-fg transition-colors duration-150"
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
