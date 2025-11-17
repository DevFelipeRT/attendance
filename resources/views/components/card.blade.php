{{-- resources/views/components/card.blade.php

Base Card component with configurable header, body and footer.
Supports props or named slots ("title", "subtitle", "actions", "footer").
If both a prop and a slot are provided for the same region, the slot takes precedence.

Props (types, defaults, notes):
- string|null $title                (null)     Header main text. Ignored if <x-slot name="title"> is used.
- string|null $subtitle             (null)     Header secondary text. Ignored if <x-slot name="subtitle"> is used.
- array<int,array{
    href:string,
    label:string,
    variant?:'solid'|'outline'|'ghost'|'link',
    classes?:string
}>|null $actions                     (null)     Header action buttons. Ignored if <x-slot name="actions"> is used.
- string|null $as                   ('div')    Root tag: 'div'|'section'|'article'|'li' etc.
- string|null $padding              ('p-6')    Padding utilities applied to header/body/footer.
- string|null $rounded              ('sm:rounded-lg rounded-lg')
- string|null $shadow               ('shadow-sm')
- 'start'|'center'|'end'|'between'|null $headerAlign    ('between') Flex justification for header content.
- 'start'|'center'|'end'|'between'|null $actionsJustify ('end')     Flex justification for the actions container.
- bool|null   $dividers             (true)     Adds top/bottom borders separating sections.
- string|null $containerClass       ('')       Additional classes on the root element.
- string|null $headerClass          ('')       Additional classes on the header wrapper.
- string|null $bodyClass            ('')       Additional classes on the body wrapper.
- string|null $footerClass          ('')       Additional classes on the footer wrapper.
- string|null $role                 (null)     ARIA role for the root container (e.g., 'region', 'group').
- string|null $ariaLabel            (null)     aria-label for accessibility when no visible title exists.

Named slots:
- title, subtitle, actions, footer

Accessibility:
- Provide $ariaLabel or a visible $title to ensure the region is identifiable by assistive technologies.
--}}

@props([
    'title'           => null,
    'subtitle'        => null,
    'actions'         => null,
    'as'              => 'div',
    'padding'         => 'p-6',
    'rounded'         => 'sm:rounded-lg rounded-lg',
    'shadow'          => 'shadow-sm',
    'headerAlign'     => 'between',
    'actionsJustify'  => 'end',
    'dividers'        => true,
    'containerClass'  => '',
    'headerClass'     => '',
    'bodyClass'       => '',
    'footerClass'     => '',
    'role'            => null,
    'ariaLabel'       => null,
])

@php
    use Illuminate\View\ComponentSlot;

    // Root tag and ARIA attributes
    $Tag = $as ?: 'div';
    $aria = [];
    if (is_string($role) && trim($role) !== '') {
        $aria[] = 'role="' . e($role) . '"';
    }
    if (is_string($ariaLabel) && trim($ariaLabel) !== '') {
        $aria[] = 'aria-label="' . e($ariaLabel) . '"';
    }
    $ariaAttr = implode(' ', $aria);

    // Alignment maps with fallback
    $map = [
        'start'   => 'justify-start',
        'center'  => 'justify-center',
        'end'     => 'justify-end',
        'between' => 'justify-between',
    ];
    $headerJustify  = $map[$headerAlign]    ?? $map['between'];
    $actionsJustify = $map[$actionsJustify] ?? $map['end'];

    // Container classes
    $container = trim(implode(' ', [
        'bg-white dark:bg-gray-800 overflow-hidden',
        $shadow,
        $rounded,
        $containerClass,
    ]));

    // Region detection (slots take precedence)
    $hasTitleSlot    = isset($title)    && $title    instanceof ComponentSlot && $title->isNotEmpty();
    $hasSubtitleSlot = isset($subtitle) && $subtitle instanceof ComponentSlot && $subtitle->isNotEmpty();
    $hasActionsSlot  = isset($actions)  && $actions  instanceof ComponentSlot && $actions->isNotEmpty();
    $hasFooterSlot   = isset($footer)   && $footer   instanceof ComponentSlot && $footer->isNotEmpty();

    $titleIsString    = is_string($title)    && trim($title)    !== '';
    $subtitleIsString = is_string($subtitle) && trim($subtitle) !== '';
    $actionsIsArray   = is_array($actions)   && !empty($actions);

    $hasHeader = $hasTitleSlot || $titleIsString || $hasSubtitleSlot || $subtitleIsString || $hasActionsSlot || $actionsIsArray;

    // Section paddings
    $headerPadding = $padding;
    $bodyPadding   = $padding;
    $footerPadding = $padding;

    // Dividers
    $borderTop    = $dividers ? 'border-t border-gray-200 dark:border-gray-700' : '';
    $borderBottom = $dividers ? 'border-b border-gray-200 dark:border-gray-700' : '';

    // Action button styles
    $ringFocus = 'focus:outline-none focus:ring-2 focus:ring-offset-0';
    $btnBase   = "inline-flex h-10 items-center rounded-md px-4 py-2 text-sm font-medium $ringFocus";
    $variants  = [
        'solid'   => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700',
        'outline' => 'border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-400 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700',
        'ghost'   => 'text-indigo-600 hover:underline focus:ring-indigo-500 dark:text-indigo-400',
        'link'    => 'text-indigo-600 underline-offset-2 hover:underline focus:ring-indigo-500 dark:text-indigo-400',
    ];
@endphp

<{{ $Tag }} class="{{ $container }}" {!! $ariaAttr !!}>
    @if($hasHeader)
        <div class="{{ $headerPadding }} {{ $headerClass }} {{ $borderBottom }}">
            <div class="flex items-start {{ $headerJustify }} gap-3">
                <div class="min-w-0">
                    @if($hasTitleSlot)
                        <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            {{ $title }}
                        </div>
                    @elseif($titleIsString)
                        <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                            {{ $title }}
                        </div>
                    @endif

                    @if($hasSubtitleSlot)
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ $subtitle }}
                        </div>
                    @elseif($subtitleIsString)
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ $subtitle }}
                        </div>
                    @endif
                </div>

                @if($hasActionsSlot || $actionsIsArray)
                    <div class="flex items-center gap-2 {{ $actionsJustify }} ml-auto">
                        @if($hasActionsSlot)
                            {{ $actions }}
                        @else
                            @foreach($actions as $a)
                                @php
                                    $href    = isset($a['href']) && is_string($a['href']) ? $a['href'] : '#';
                                    $label   = isset($a['label']) && is_string($a['label']) ? $a['label'] : 'Action';
                                    $variant = isset($a['variant']) && is_string($a['variant']) ? $a['variant'] : 'solid';
                                    $extra   = isset($a['classes']) && is_string($a['classes']) ? $a['classes'] : '';
                                    $classes = trim($btnBase . ' ' . ($variants[$variant] ?? $variants['solid']) . ' ' . $extra);
                                @endphp
                                <a href="{{ $href }}" class="{{ $classes }}">{{ $label }}</a>
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="{{ $bodyPadding }} {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if($hasFooterSlot)
        <div class="{{ $borderTop }} {{ $footerPadding }} {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</{{ $Tag }}>
