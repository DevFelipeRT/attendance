@php
    use App\Models\ClassGroup\ClassGroup;
    use App\Models\ClassGroup\ClassLesson;

    /** @var ClassGroup $classGroup */
    /** @var \Illuminate\Support\Collection<int, ClassLesson> $lessons */
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-text dark:text-text-inverse leading-tight">
            Class group · {{ $classGroup->name }}
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
            href="{{ route('class-groups.index') }}"
            variant="ghost"
            size="sm"
            :responsive=false
        >
            Back to list
        </x-button>

        {{-- Class group summary --}}
        <div
            class="bg-surface-base dark:bg-surface-inverse
                   rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse
                   p-6"
        >
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Name
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse">
                            {{ $classGroup->name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Subject
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse">
                            {{ $classGroup->subject->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Teacher
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse">
                            {{ $classGroup->teacher->name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Term
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse">
                            @php
                                $start = optional($classGroup->term_start_date)?->format('d/m/Y');
                                $end   = optional($classGroup->term_end_date)?->format('d/m/Y');
                            @endphp
                            {{ $start ?? '—' }} – {{ $end ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-text-subtle dark:text-text-inverse-subtle">
                            Default lesson duration
                        </dt>
                        <dd class="text-sm text-text dark:text-text-inverse tabular-nums">
                            @if (! is_null($classGroup->default_lesson_duration_minutes))
                                {{ $classGroup->default_lesson_duration_minutes }} minutes
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="flex flex-col sm:flex-row flex-wrap justify-end items-center gap-3">
                    <x-button
                        as="a"
                        href="{{ route('class-groups.edit', $classGroup) }}"
                        variant="default"
                        size="sm"
                    >
                        Edit group
                    </x-button>

                    <x-button
                        as="a"
                        href="{{ route('class-groups.enrollments.index', $classGroup) }}"
                        variant="primary"
                        size="sm"
                    >
                        View enrollments
                    </x-button>
                </div>
            </div>

            <p class="mt-2 text-xs text-text-subtle dark:text-text-inverse-subtle">
                Class groups use this configuration as a base for generated lessons and attendance tracking.
            </p>
        </div>

        {{-- Generate lessons from schedule --}}
        <section aria-labelledby="class-lessons-generate-heading">
            <div class="flex items-center justify-between mb-3">
                <h2
                    id="class-lessons-generate-heading"
                    class="text-sm font-semibold text-text-subtle dark:text-text-inverse-subtle"
                >
                    Generate lessons from schedule
                </h2>
            </div>

            <div
                class="bg-surface-base dark:bg-surface-inverse
                       rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse
                       px-6 py-6"
            >
                <p class="text-xs text-text-muted dark:text-text-inverse-muted mb-4">
                    Automatically generate lessons based on the weekly schedule and period of this class group.
                </p>

                <form
                    method="POST"
                    action="{{ route('class-groups.lessons.generate', $classGroup) }}"
                    class="space-y-4"
                >
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="from" value="From date" />
                            <x-text-input
                                id="from"
                                name="from"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('from', optional($classGroup->term_start_date)?->format('Y-m-d'))"
                            />
                            <x-input-error :messages="$errors->get('from')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="to" value="To date" />
                            <x-text-input
                                id="to"
                                name="to"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('to', optional($classGroup->term_end_date)?->format('Y-m-d'))"
                            />
                            <x-input-error :messages="$errors->get('to')" class="mt-2" />
                        </div>

                        <div class="flex items-end">
                            <x-button
                                type="submit"
                                variant="primary"
                                size="sm"
                            >
                                Generate lessons
                            </x-button>
                        </div>
                    </div>
                </form>

                @if ($errors->has('from') || $errors->has('to'))
                    <div
                        class="mt-4 rounded-xl border border-status-error-border
                               bg-status-error-softBg px-4 py-3 text-xs
                               text-status-error-subtleFg"
                    >
                        Please review the generation dates.
                    </div>
                @endif
            </div>
        </section>

        {{-- Lessons: embedded index list --}}
        <section class="space-y-3" aria-labelledby="class-lessons-heading">
            <div class="flex gap-3 items-end justify-between">
                <h2
                    id="class-lessons-heading"
                    class="text-sm font-semibold text-text-subtle dark:text-text-inverse-subtle"
                >
                    Lessons
                </h2>

            <x-button
                as="a"
                href="{{ route('class-groups.lessons.create', $classGroup) }}"
                variant="primary"
                size="sm"
                :responsive=false
                class="mb-1"
            >
                New lesson
            </x-button>
            </div>

            <div
                class="bg-surface-base dark:bg-surface-inverse
                       rounded-2xl shadow-card border border-border-subtle dark:border-border-inverse"
            >
                @if ($lessons->isEmpty())
                    <p class="px-4 py-6 text-sm text-text-muted dark:text-text-inverse-muted">
                        No lessons have been created for this class group yet.
                    </p>
                @else
                    <div class="overflow-x-auto rounded-2xl">
                        <table
                            class="w-full table-auto divide-y divide-border-subtle
                                   dark:divide-border-inverse"
                        >
                            <thead class="bg-surface-alt dark:bg-surface-inverse-alt">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-3 py-2 text-left text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wide whitespace-nowrap"
                                    >
                                        Date
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-2 text-left text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wide whitespace-nowrap"
                                    >
                                        Time
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-2 text-left text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wide whitespace-nowrap"
                                    >
                                        Duration
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-3 py-2 text-left text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wide whitespace-nowrap hidden sm:table-cell"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
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
                                @foreach ($lessons as $lesson)
                                    <tr
                                        class="group cursor-pointer transition-colors duration-150
                                               hover:bg-surface-alt/30 dark:hover:bg-surface-inverse-alt"
                                        onclick="window.location='{{ route('class-groups.lessons.attendance.edit', [$classGroup, $lesson]) }}'"
                                    >
                                        {{-- Date --}}
                                        <td
                                            class="px-3 py-2 text-sm text-text dark:text-text-inverse
                                                   whitespace-nowrap tabular-nums"
                                        >
                                            {{ $lesson->lesson_date?->format('d/m/Y') ?? '—' }}
                                        </td>

                                        {{-- Time --}}
                                        <td
                                            class="px-3 py-2 text-sm text-text dark:text-text-inverse
                                                   whitespace-nowrap tabular-nums"
                                        >
                                            @if ($lesson->start_time)
                                                {{ \Illuminate\Support\Carbon::parse($lesson->start_time)->format('H:i') }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        {{-- Duration --}}
                                        <td
                                            class="px-3 py-2 text-sm text-text dark:text-text-inverse
                                                   whitespace-nowrap tabular-nums"
                                        >
                                            @if ($lesson->duration_minutes)
                                                {{ $lesson->duration_minutes }} min
                                            @else
                                                —
                                            @endif
                                        </td>

                                        {{-- Status (badge, hidden on mobile) --}}
                                        <td class="px-3 py-2 text-sm hidden sm:table-cell align-center whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                       bg-background-subtle dark:bg-surface-subtle/20
                                                       text-text-muted dark:text-text-inverse-muted
                                                       capitalize"
                                            >
                                                {{ $lesson->status?->value ?? 'scheduled' }}
                                            </span>
                                        </td>

                                        <td class="px-2 py-2 text-sm text-right whitespace-nowrap align-top w-0">
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
                                                        triggerAriaLabel="Open lesson actions"
                                                        width="w-40"
                                                        :flipGuard="200"
                                                        :items="[
                                                            [
                                                                'type'  => 'link',
                                                                'label' => 'Edit lesson',
                                                                'href'  => route('class-groups.lessons.edit', [$classGroup, $lesson]),
                                                            ],
                                                            [
                                                                'type'    => 'form',
                                                                'label'   => 'Cancel lesson',
                                                                'action'  => route('class-groups.lessons.cancel', [$classGroup, $lesson]),
                                                                'method'  => 'POST',
                                                                'confirm' => 'Cancelling the lesson will also delete the attendance permanently. Would you like to continue?',
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
                                                        href="{{ route('class-groups.lessons.edit', [$classGroup, $lesson]) }}"
                                                        variant="outline"
                                                        size="xs"
                                                    >
                                                        Edit
                                                    </x-button>

                                                    <form
                                                        action="{{ route('class-groups.lessons.cancel', [$classGroup, $lesson]) }}"
                                                        method="POST"
                                                        onsubmit="event.stopPropagation(); return confirm('Cancelling the lesson will also delete the attendance permanently. Would you like to continue?');"
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
