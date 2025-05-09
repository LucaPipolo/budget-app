@props([
    'submit',
])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:col-span-2 md:mt-0">
        <form wire:submit="{{ $submit }}">
            <div
                class="{{ isset($actions) ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md' }} bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6"
            >
                <div class="grid grid-cols-6 gap-6">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div
                    class="flex items-center justify-end bg-gray-50 px-4 py-3 text-end shadow dark:bg-gray-800 sm:rounded-bl-md sm:rounded-br-md sm:px-6"
                >
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
