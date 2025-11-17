{{-- resources/views/components/input-error.blade.php --}}
@props([
    'messages' => [],
])

@if ($messages)
    <ul
        {{ $attributes->merge([
            'class' => 'mt-1 text-xs text-status-error-subtleFg space-y-1',
        ]) }}
    >
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
