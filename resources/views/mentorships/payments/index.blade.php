{{-- resources/views/mentorships/payments/index.blade.php --}}

@php
    /** @var \App\Models\Mentorship\Mentorship $mentorship */
    /** @var \Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator $payments */

    $student = $mentorship->student;

    $paymentsCount = $payments instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
        ? $payments->total()
        : $payments->count();

    $totalAmount = ($payments instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
        ? $payments->getCollection()->sum('amount')
        : $payments->sum('amount');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Mentorship payments – {{ $student->name ?? 'Mentorship' }}
        </h1>
    </x-slot>

    <div class="space-y-6">
        <x-button
            as="a"
            href="{{ route('mentorships.show', $mentorship) }}"
            variant="ghost"
            size="sm"
            :responsive=false
        >
            Back to mentorship
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
                        Student: {{ $student->name ?? '—' }}
                        @if ($mentorship->subject)
                            • Subject: {{ $mentorship->subject->name }}
                        @endif
                        @if ($mentorship->teacher)
                            • Teacher: {{ $mentorship->teacher->name }}
                        @endif
                    </p>
                    <p class="mt-1 text-sm text-text-muted dark:text-text-inverse-muted">
                        Hourly rate:
                        @if (! is_null($mentorship->hourly_rate))
                            R$ {{ number_format((float) $mentorship->hourly_rate, 2, ',', '.') }}
                        @else
                            Not defined
                        @endif
                    </p>
                </div>

                
            </div>

            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Payments count
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            {{ $paymentsCount }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Total amount paid
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse tabular-nums">
                            R$ {{ number_format((float) $totalAmount, 2, ',', '.') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-text-subtle dark:text-text-inverse-subtle">
                            Last payment
                        </dt>
                        <dd class="mt-1 text-sm text-text dark:text-text-inverse">
                            @php
                                $last = ($payments instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                                    ? $payments->getCollection()->sortByDesc('paid_at')->first()
                                    : $payments->sortByDesc('paid_at')->first();
                            @endphp
                            {{ optional($last?->paid_at)?->format('d/m/Y H:i') ?? '—' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Payments list --}}
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                   border border-border-subtle dark:border-border-inverse overflow-hidden"
        >
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                       flex items-center justify-between
                       bg-surface-alt dark:bg-surface-inverse-alt"
            >
                <h3 class="text-sm font-semibold text-text dark:text-text-inverse">
                    Payments
                </h3>
                
                <div class="flex flex-col sm:flex-row flex-wrap justify-center sm:justify-end items-end sm:items-start gap-3">
                    <x-button
                        as="a"
                        href="{{ route('mentorships.payments.create', $mentorship) }}"
                        variant="primary"
                        size="sm"
                    >
                        New payment
                    </x-button>
                </div>
            </div>


            <div class="px-6 py-6">
                @if (session('status'))
                    <div
                        class="mb-4 rounded-2xl border border-status-success-border
                               bg-status-success-softBg px-4 py-3 text-sm text-status-success-subtleFg"
                    >
                        {{ session('status') }}
                    </div>
                @endif

                @php
                    $isEmpty = $payments->isEmpty();
                @endphp

                @if ($isEmpty)
                    <p class="text-sm text-text-muted dark:text-text-inverse-muted">
                        No payments registered for this mentorship.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border-strong dark:divide-border-inverse-strong">
                            <thead class="bg-background-muted dark:bg-background-inverse-muted">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wider"
                                    >
                                        Amount
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wider"
                                    >
                                        Hours
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wider"
                                    >
                                        Paid at
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-2 text-left text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wider"
                                    >
                                        Created at
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-4 py-2 text-right text-xs font-medium
                                               text-text-subtle dark:text-text-inverse-subtle
                                               uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody
                                class="divide-y divide-border-subtle dark:divide-border-inverse
                                       bg-surface-base dark:bg-surface-inverse"
                            >
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-text dark:text-text-inverse tabular-nums">
                                            R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-text dark:text-text-inverse tabular-nums">
                                            {{ (int) $payment->hours }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-text-muted dark:text-text-inverse-muted">
                                            {{ optional($payment->paid_at)?->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-text-muted dark:text-text-inverse-muted">
                                            {{ optional($payment->created_at)?->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <x-button
                                                    as="a"
                                                    href="{{ route('mentorships.payments.show', [$mentorship, $payment]) }}"
                                                    variant="outline"
                                                    size="sm"
                                                >
                                                    View
                                                </x-button>

                                                <form
                                                    action="{{ route('mentorships.payments.destroy', [$mentorship, $payment]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Delete this payment? This will adjust the mentorship balance.');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <x-button
                                                        type="submit"
                                                        variant="danger"
                                                        size="sm"
                                                    >
                                                        Delete
                                                    </x-button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($payments instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                        <div class="mt-4">
                            {{ $payments->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
