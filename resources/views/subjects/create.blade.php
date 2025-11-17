{{-- resources/views/subjects/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text dark:text-text-inverse leading-tight">
            New subject
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-surface-base dark:bg-surface-inverse shadow-card rounded-2xl
                       border border-border-subtle dark:border-border-inverse"
            >
                <div
                    class="px-6 py-4 border-b border-border-subtle dark:border-border-inverse
                           bg-surface-alt dark:bg-surface-inverse-alt rounded-t-2xl"
                >
                    <h3 class="text-lg font-medium text-text dark:text-text-inverse">
                        Create subject
                    </h3>
                </div>

                <div class="px-6 py-6">
                    @if ($errors->any())
                        <div
                            class="mb-4 rounded-2xl border border-status-error-border
                                   bg-status-error-softBg px-4 py-3 text-sm text-status-error-subtleFg"
                        >
                            Please fix the errors and try again.
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('subjects.store') }}"
                        class="space-y-6"
                    >
                        @csrf

                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input
                                id="name"
                                name="name"
                                type="text"
                                class="mt-1"
                                :value="old('name')"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
                            <x-button
                                as="a"
                                href="{{ route('subjects.index') }}"
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
                                Save
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
