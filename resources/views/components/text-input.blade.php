{{-- resources/views/components/text-input.blade.php --}}
@props([
    'disabled' => false,
])

@php
    $baseClasses = implode(' ', [
        'block w-full',
        'rounded-2xl',
        'border border-border-subtle dark:border-border-inverse',
        'bg-surface-base dark:bg-surface-inverse',
        'text-sm text-text dark:text-text-inverse',
        'placeholder:text-text-subtle dark:placeholder:text-text-inverse-subtle',
        'shadow-sm',
        'focus:border-action-primary-bg',
        'focus:ring-2 focus:ring-action-primary-ring',
        'focus:ring-offset-2',
        'focus:ring-offset-background-muted dark:focus:ring-offset-background-inverse',
        'transition ease-in-out duration-150',
        'disabled:opacity-60 disabled:cursor-not-allowed',
    ]);
@endphp

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => $baseClasses]) !!}
>
