{{-- resources/views/subjects/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Subjects
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

        {{-- Subjects: index list --}}
        <section class="space-y-4" aria-labelledby="subjects-heading">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-between">
                <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                    Manage subjects available for class groups and mentorships.
                </p>

                <div class="flex w-full sm:w-fit justify-end">
                    <x-button
                        as="a"
                        href="{{ route('subjects.create') }}"
                        variant="primary"
                        size="sm"
                        :responsive=false
                    >
                        New subject
                    </x-button>
                </div>
            </div>

            <div
                class="bg-surface-base dark:bg-surface-inverse
                       rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse"
            >
                <div class="overflow-x-auto rounded-2xl">
                    <table
                        class="min-w-full divide-y divide-border-subtle
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
                            @forelse ($subjects as $subject)
                                <tr
                                    class="group cursor-pointer transition-colors duration-150
                                           hover:bg-surface-alt/30 dark:hover:bg-surface-inverse-alt"
                                    onclick="window.location='{{ route('subjects.edit', $subject) }}'"
                                >
                                    <td class="px-4 py-3 text-sm text-text dark:text-text-inverse">
                                        {{ $subject->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
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
                                        colspan="2"
                                        class="px-4 py-6 text-center text-sm text-text-muted dark:text-text-inverse-muted"
                                    >
                                        No subjects registered.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($subjects, 'links'))
                    <div class="px-4 py-3 border-t border-border-subtle dark:border-border-inverse">
                        {{ $subjects->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
