{{-- resources/views/mentorships/payments/create.blade.php --}}

@php
    /** @var \App\Models\Mentorship\Mentorship $mentorship */
@endphp

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-text dark:text-text-inverse leading-tight">
            New mentorship payment – {{ $mentorship->student->name ?? 'Mentorship' }}
        </h1>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                   border border-border-subtle dark:border-border-inverse"
        >
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                       bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
            >
                <h3 class="text-lg font-medium text-text dark:text-text-inverse">
                    Register payment
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
                    @if (! is_null($mentorship->hourly_rate))
                        R$ {{ number_format((float) $mentorship->hourly_rate, 2, ',', '.') }}
                    @else
                        Not defined on mentorship
                    @endif
                </p>
            </div>

            <div class="px-6 py-6 space-y-6">
                @if ($errors->any())
                    <x-alert variant="error" size="sm">
                        There are validation errors in the form. Please review the fields.
                    </x-alert>
                @endif

                <form
                    method="POST"
                    action="{{ route('mentorships.payments.store', $mentorship) }}"
                    class="space-y-6"
                >
                    @csrf

                    <div>
                        <x-input-label
                            for="amount"
                            value="Amount (R$)"
                            class="text-text dark:text-text-inverse"
                        />
                        <x-text-input
                            id="amount"
                            name="amount"
                            type="number"
                            step="0.01"
                            min="0"
                            class="mt-1 block w-full"
                            :value="old('amount')"
                            required
                        />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        <p class="mt-1 text-xs text-text-subtle dark:text-text-inverse-subtle">
                            The service converts this amount into whole-hour credits using the current hourly rate.
                        </p>
                    </div>

                    <div>
                        <x-input-label
                            for="paid_at"
                            value="Paid at"
                            class="text-text dark:text-text-inverse"
                        />
                        <x-text-input
                            id="paid_at"
                            name="paid_at"
                            type="datetime-local"
                            class="mt-1 block w-full"
                            :value="old('paid_at')"
                        />
                        <x-input-error :messages="$errors->get('paid_at')" class="mt-2" />
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
                        <x-button
                            as="a"
                            href="{{ route('mentorships.payments.index', $mentorship) }}"
                            variant="ghost"
                            size="sm"
                        >
                            Cancel
                        </x-button>

                        <x-button
                            type="submit"
                            variant="primary"
                            size="sm"
                        >
                            Save payment
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
