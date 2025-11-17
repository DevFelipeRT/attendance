{{-- resources/views/mentorships/payments/show.blade.php --}}

@php
    /** @var \App\Models\Mentorship\Mentorship $mentorship */
    /** @var \App\Models\Mentorship\MentorshipPayment $payment */
@endphp

<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Mentorship payment – {{ $mentorship->student->name ?? 'Mentorship' }}
        </h1>
    </x-slot>

    <div class="space-y-6">

        <x-button
            as="a"
            href="{{ route('mentorships.payments.index', $mentorship) }}"
            variant="ghost"
            size="sm"
            :responsive=false
        >
            Back to payments
        </x-button>
        {{-- Context card --}}
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                   border border-border-subtle dark:border-border-inverse"
        >
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                       flex flex-col gap-3 md:flex-row md:justify-between
                       bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
            >
                <div>
                    <h3 class="text-lg font-medium text-text dark:text-text-inverse">
                        Mentorship context
                    </h3>
                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        Student: {{ $mentorship->student->name ?? '—' }}
                        @if ($mentorship->subject)
                            • Subject: {{ $mentorship->subject->name }}
                        @endif
                        @if ($mentorship->teacher)
                            • Teacher: {{ $mentorship->teacher->name }}
                        @endif
                    </p>
                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        Hourly rate:
                        R$ {{ number_format((float) $mentorship->hourly_rate, 2, ',', '.') }}
                        • Period:
                        {{ optional($mentorship->started_at)?->format('d/m/Y') ?? '—' }}
                        –
                        {{ optional($mentorship->ended_at)?->format('d/m/Y') ?? '—' }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row flex-wrap justify-center sm:justify-end items-end sm:items-start gap-3">      
                    <form
                        action="{{ route('mentorships.payments.destroy', [$mentorship, $payment]) }}"
                        method="POST"
                        onsubmit="return confirm('Delete this payment? This will adjust the mentorship balance.');"
                        class="w-full sm:w-fit"
                    >
                        @csrf
                        @method('DELETE')

                        <x-button
                            type="submit"
                            variant="danger"
                            size="sm"
                        >
                            Delete payment
                        </x-button>
                    </form>
                </div>
            </div>

            <div class="px-6 py-6 space-y-6">
                @if (session('status'))
                    <div
                        class="rounded-2xl border border-status-success-border
                               bg-status-success-softBg px-4 py-3 text-sm text-status-success-subtleFg"
                    >
                        {{ session('status') }}
                    </div>
                @endif

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Amount
                        </dt>
                        <dd class="mt-1 text-lg font-semibold text-text dark:text-text-inverse tabular-nums">
                            R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Hours credited
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse tabular-nums">
                            {{ (int) $payment->hours }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Paid at
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            {{ optional($payment->paid_at)?->format('d/m/Y H:i') ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Created at
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            {{ optional($payment->created_at)?->format('d/m/Y H:i') ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Updated at
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            {{ optional($payment->updated_at)?->format('d/m/Y H:i') ?? '—' }}
                        </dd>
                    </div>
                </dl>

                <p class="mt-4 text-xs text-text-subtle dark:text-text-inverse-subtle">
                    This payment’s amount is converted into whole-hour credits using the mentorship’s hourly rate.
                    Exact conversion is enforced; invalid conversions are rejected by the billing service.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
