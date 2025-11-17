{{-- resources/views/components/toast-container.blade.php
    Toast container responsible for positioning and stacking toasts. It stays out of document flow.

    Props:
    - string|null $placement       top-right|top-center|top-left|bottom-right|bottom-center|bottom-left (default: top-center)
    - string|null $gap             Tailwind gap utility for the stack (default: gap-3)
    - string|null $padding         Tailwind padding utilities for the container (default: p-4 sm:p-6)
    - string|null $stackDirection  col or row (default: col)
    - string|null $alignItems      Tailwind align-items override; inferred from placement when null
    - array|null  $toasts          Explicit toasts; merged with session flashes

    Notes:
    - Uses a very high z-index to ensure it sits above modals/backdrops.
    - Neutral container; color theming is handled by <x-toast> for light/dark modes.
--}}

@props([
    'placement'       => 'top-center',
    'gap'             => 'gap-3',
    'padding'         => 'p-4 sm:p-6',
    'stackDirection'  => 'col',
    'alignItems'      => null,
    'toasts'          => null,
])

@php
    $flashKeys = ['success', 'error', 'info', 'warning'];
    $collected = [];

    foreach ($flashKeys as $key) {
        $val = session($key);

        if (is_string($val) && $val !== '') {
            $collected[] = ['type' => $key, 'message' => $val];
            continue;
        }

        if (is_array($val)) {
            foreach ($val as $m) {
                if (is_string($m) && $m !== '') {
                    $collected[] = ['type' => $key, 'message' => $m];
                }
            }
        }
    }

    if (is_array($toasts)) {
        foreach ($toasts as $t) {
            if (
                is_array($t)
                && isset($t['type'], $t['message'])
                && is_string($t['type'])
                && is_string($t['message'])
                && $t['message'] !== ''
            ) {
                $collected[] = ['type' => $t['type'], 'message' => $t['message']];
            }
        }
    }

    $pos = match ($placement) {
        'top-center'    => 'top-2 left-1/2 -translate-x-1/2',
        'top-left'      => 'top-2 left-4',
        'top-right'     => 'top-2 right-4',
        'bottom-right'  => 'bottom-4 right-4',
        'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2',
        'bottom-left'   => 'bottom-4 left-4',
        default         => 'top-2 right-4',
    };

    $inferredAlign = str_contains((string) $placement, 'left')
        ? 'items-start'
        : (str_contains((string) $placement, 'center') ? 'items-center' : 'items-end');

    $align     = $alignItems ?: $inferredAlign;
    $direction = $stackDirection === 'row' ? 'flex-row' : 'flex-col';

    // Fixed wrapper with extreme z-index and isolated stacking context to sit above modals/backdrops.
    $outerClass = trim("fixed z-[2147483647] $pos $padding pointer-events-none [isolation:isolate]");

    // Inner stack where user classes may be merged safely.
    $innerBaseClass = trim("flex $direction $align $gap w-full max-w-sm sm:max-w-md lg:max-w-lg pointer-events-auto");
@endphp

@if(!empty($collected))
<div
    class="{{ $outerClass }} w-full max-w-sm sm:max-w-md lg:max-w-lg"
    x-data="{}"
    x-cloak
    role="region"
    aria-live="polite"
    aria-relevant="additions"
>
    <div {{ $attributes->class($innerBaseClass) }}>
        @foreach($collected as $toast)
            {{-- <x-toast> should define its own light/dark palette. --}}
            <x-toast :type="$toast['type']" :message="$toast['message']" />
        @endforeach
    </div>
</div>
@endif
