{{-- resources/views/components/alert.blade.php --}}
@props([
    'variant' => 'info',   // info | success | warning | error
    'title'   => null,
])

@php
    $baseClasses = 'relative w-full rounded-2xl border px-4 py-3 text-sm flex items-start gap-3';

    $variantClasses = [
        'success' => 'bg-status-success-softBg dark:bg-transparent border-status-success-border text-status-success-subtleFg',
        'error'   => 'bg-status-error-softBg dark:bg-transparent border-status-error-border text-status-error-subtleFg',
        'warning' => 'bg-status-warning-softBg dark:bg-transparent border-status-warning-border text-status-warning-subtleFg',
        'info'    => 'bg-status-info-softBg dark:bg-transparent border-status-info-border text-status-info-subtleFg',
    ];

    $classes = $variantClasses[$variant] ?? $variantClasses['info'];
@endphp

<div
    role="status"
    {{ $attributes->merge(['class' => $baseClasses . ' ' . $classes]) }}
>
    @isset($icon)
        <div class="mt-0.5 flex-shrink-0">
            {{ $icon }}
        </div>
    @endisset

    <div class="flex-1">
        @if ($title)
            <div class="text-sm font-semibold leading-snug">
                {{ $title }}
            </div>
        @endif

        <div class="mt-0.5 text-sm leading-snug">
            {{ $slot }}
        </div>
    </div>

    @isset($actions)
        <div class="ms-3 flex-shrink-0 flex items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
