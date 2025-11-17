{{-- resources/views/dashboard/partials/class-group-alerts-card.blade.php --}}

@php
    /**
     * Expected variables in scope:
     *
     * @var \Illuminate\Support\Collection|null $classGroupAlerts
     *
     * Each alert item is expected to be an array-like structure:
     * - 'class_group' => \App\Models\ClassGroup\ClassGroup|null
     * - 'reason'      => string|null  (short description of why this group needs attention)
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
            Class group alerts
        </h3>
        <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
            Class groups requiring attention (terms, schedule or attendance).
        </p>
    </div>

    <div class="px-6 py-6 space-y-4">
        @if (!isset($classGroupAlerts) || $classGroupAlerts->isEmpty())
            <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                There are no class group alerts at the moment.
            </p>
        @else
            <ul class="space-y-3">
                @foreach ($classGroupAlerts as $alert)
                    @php
                        /** @var array|\ArrayAccess $alert */
                        $classGroup = $alert['class_group'] ?? null;
                        $reason     = isset($alert['reason']) ? (string) $alert['reason'] : null;

                        $isClickable = $classGroup !== null;
                    @endphp

                    <li>
                        @if ($isClickable)
                            <a
                                href="{{ route('class-groups.show', $classGroup) }}"
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
                                            {{ $classGroup?->name ?? 'Class group' }}
                                        </p>

                                        @if ($reason !== null && $reason !== '')
                                            <p
                                                class="mt-0.5 min-w-0 text-xs text-text-muted dark:text-text-inverse-muted
                                                       break-words hyphens-auto line-clamp-3"
                                            >
                                                {{ $reason }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center">
                                        @if ($isClickable)
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
                        @if ($isClickable)
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
