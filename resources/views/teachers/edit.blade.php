{{-- resources/views/teachers/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            Edit teacher
        </h1>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 py-6">
        <div
            class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                   border border-border-subtle dark:border-border-inverse"
        >
            <div
                class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                       flex items-center justify-between
                       bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
            >
                <h2 class="text-lg font-medium text-text dark:text-text-inverse">
                    Update teacher
                </h2>

                <x-button
                    as="a"
                    href="{{ route('teachers.index') }}"
                    variant="ghost"
                    size="sm"
                >
                    Back to list
                </x-button>
            </div>

            <div class="px-6 py-6">
                @if (session('status'))
                    <div
                        class="mb-4 rounded-2xl border border-status-success-border
                               bg-status-success-softBg px-4 py-3 text-sm
                               text-status-success-subtleFg"
                    >
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mb-4 rounded-2xl border border-status-error-border
                               bg-status-error-softBg px-4 py-3 text-sm
                               text-status-error-subtleFg"
                    >
                        Please fix the errors and try again.
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('teachers.update', $teacher) }}"
                    class="space-y-6"
                >
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label
                            for="name"
                            value="Name"
                            class="text-text dark:text-text-inverse"
                        />
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('name', $teacher->name)"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-button
                            as="a"
                            href="{{ route('teachers.index') }}"
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
                            Update
                        </x-button>
                    </div>
                </form>

                <hr class="my-6 border-border-subtle dark:border-border-inverse">

                <form
                    method="POST"
                    action="{{ route('teachers.destroy', $teacher) }}"
                    onsubmit="return confirm('Delete this teacher?');"
                >
                    @csrf
                    @method('DELETE')

                    <x-button
                        type="submit"
                        variant="danger"
                        size="sm"
                    >
                        Delete teacher
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
