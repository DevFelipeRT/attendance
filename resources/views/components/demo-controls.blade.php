{{-- resources/views/components/demo-controls.blade.php --}}

@php
    $btn = 'inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700';
@endphp

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-md">
    <div class="flex flex-col sm:flex-row w-full items-center justify-end gap-2 sm:gap-4 p-6">
        <form action="{{ route('demo.reset') }}" method="POST" class="flex-1 w-full max-w-xs">
            @csrf
            <button type="submit" class="{{ $btn }} w-full justify-center">Reset demo</button>
        </form>
        <form action="{{ route('demo.end') }}" method="POST" class="flex-1 w-full max-w-xs">
            @csrf
            <button type="submit" class="{{ $btn }} w-full justify-center">End demo</button>
        </form>
    </div>
</div>
