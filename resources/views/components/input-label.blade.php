{{-- resources/views/components/input-label.blade.php --}}
@props([
    'value' => null,
])

<label
    {{ $attributes->merge([
        'class' => 'block text-sm font-medium text-text dark:text-text-inverse',
    ]) }}
>
    {{ $value ?? $slot }}
</label>
