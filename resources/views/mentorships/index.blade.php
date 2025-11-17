{{-- Mentorships: index list --}}
{{-- Displays a paginated list of mentorship contracts with key attributes and actions. --}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Mentorship list
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

        {{-- Mentorships: index list --}}
        <section class="space-y-4" aria-labelledby="mentorships-heading">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-between">
                <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                    Manage one-to-one mentorship contracts, their subjects and teachers.
                </p>

                <div class="flex w-full sm:w-fit justify-end">
                    <x-button
                        as="a"
                        href="{{ route('mentorships.create') }}"
                        variant="primary"
                        size="sm"
                        :responsive=false
                    >
                        New mentorship
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
                                    Student
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
                                           uppercase tracking-wide"
                                >
                                    Subject
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium
                                           text-text-subtle dark:text-text-inverse-subtle
                                           uppercase tracking-wide hidden sm:table-cell"
                                >
                                    Status
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
                            @forelse ($items as $item)
                                <tr
                                    class="min-w-0 group cursor-pointer transition-colors duration-150
                                           hover:bg-surface-alt/30 dark:hover:bg-surface-inverse-alt"
                                    onclick="window.location='{{ route('mentorships.show', $item) }}'"
                                >
                                    {{-- Student --}}
                                    <td class="px-4 py-3 align-top">
                                        <div class="min-w-0">
                                            <p
                                                class="min-w-0 text-sm text-text dark:text-text-inverse
                                                       truncate text-pretty hyphens-auto line-clamp-3"
                                            >
                                                {{ $item->student->name ?? '—' }}
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
                                                {{ $item->subject->name ?? '—' }}
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
                                                {{ $item->teacher->name ?? '—' }}
                                            </p>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3 text-sm hidden sm:table-cell align-top">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                   bg-background-subtle dark:bg-surface-inverse-subtle
                                                   text-text-muted dark:text-text-inverse-muted whitespace-nowrap capitalize"
                                        >
                                            {{ $item->status ?? 'active' }}
                                        </span>
                                    </td>

                                    {{-- Chevron indicator --}}
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
                                        No mentorships found. Create the first mentorship.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($items, 'links'))
                    <div class="px-4 py-3 border-t border-border-subtle dark:border-border-inverse">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
