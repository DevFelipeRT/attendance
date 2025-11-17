<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Class group list
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

        {{-- Class groups: index list --}}
        <section class="space-y-4" aria-labelledby="class-groups-heading">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-between">
                <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                    Manage regular class groups, their subjects, teachers and terms.
                </p>

                <div class="flex w-full sm:w-fit justify-end">
                    <x-button
                        as="a"
                        href="{{ route('class-groups.create') }}"
                        variant="primary"
                        size="sm"
                        :responsive=false
                    >
                        New class group
                    </x-button>
                </div>
            </div>

            <div
                class="bg-surface-base dark:bg-surface-inverse
                       rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse"
            >
                <div class="overflow-x-auto rounded-2xl">
                    <table
                        class="w-full table-fixed divide-y divide-border-subtle
                               dark:divide-border-inverse"
                    >
                        <thead class="bg-surface-alt dark:bg-surface-inverse-alt">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide"
                                >
                                    Name
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide"
                                >
                                    Subject
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide"
                                >
                                    Teacher
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide hidden sm:table-cell"
                                >
                                    Term
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-right text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide"
                                >
                                    <span class="sr-only">Open</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-border-subtle dark:divide-border-inverse
                                   bg-surface-base dark:bg-surface-inverse"
                        >
                            @forelse ($classGroups as $classGroup)
                                <tr
                                    class="group cursor-pointer transition-colors duration-150
                                           hover:bg-surface-alt/30 dark:hover:bg-surface-inverse-alt"
                                    onclick="window.location='{{ route('class-groups.show', $classGroup) }}'"
                                >
                                    {{-- Name --}}
                                    <td class="px-4 py-3 align-top">
                                        <div class="min-w-0">
                                            <p
                                                class="min-w-0 text-sm text-text dark:text-text-inverse
                                                       break-words hyphens-auto line-clamp-3"
                                            >
                                                {{ $classGroup->name }}
                                            </p>
                                        </div>
                                    </td>

                                    {{-- Subject --}}
                                    <td class="px-4 py-3 align-top">
                                        <div class="min-w-0">
                                            <p
                                                class="min-w-0 text-sm text-text dark:text-text-inverse
                                                       truncate text-pretty hyphens-auto line-clamp-3"
                                            >
                                                {{ $classGroup->subject->name ?? '—' }}
                                            </p>
                                        </div>
                                    </td>

                                    {{-- Teacher --}}
                                    <td class="px-4 py-3 align-top">
                                        <div class="min-w-0">
                                            <p
                                                class="min-w-0 text-sm text-text dark:text-text-inverse
                                                       truncate text-pretty hyphens-auto line-clamp-3"
                                            >
                                                {{ $classGroup->teacher->name ?? '—' }}
                                            </p>
                                        </div>
                                    </td>

                                    {{-- Term --}}
                                    <td class="px-4 py-3 text-sm text-text-muted dark:text-text-inverse-muted hidden sm:table-cell align-top">
                                        @php
                                            $start = optional($classGroup->term_start_date)?->format('d/m/Y');
                                            $end   = optional($classGroup->term_end_date)?->format('d/m/Y');
                                        @endphp
                                        {{ $start ?? '—' }} – {{ $end ?? '—' }}
                                    </td>

                                    {{-- Chevron --}}
                                    <td class="px-4 py-3 text-sm text-right align-top">
                                        <svg
                                            class="inline-block h-4 w-4 text-text-muted dark:text-text-inverse-muted
                                                   group-hover:text-text dark:group-hover:text-action-primary-fg transition-colors duration-150"
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-4 py-6 text-center text-sm text-text-muted dark:text-text-inverse-muted"
                                    >
                                        No class groups registered.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($classGroups, 'links'))
                    <div class="px-4 py-3 border-t border-border-subtle dark:border-border-inverse">
                        {{ $classGroups->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
