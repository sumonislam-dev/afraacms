<x-frontend-layout>
    <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6">
        <h1 class="text-3xl font-bold">
            {{ setting('tagline') ?: __('Welcome to :name', ['name' => setting('site_name', config('app.name', 'AfraaCMS'))]) }}
        </h1>
        <p class="mt-4 text-gray-600">
            {{ __('This homepage is a placeholder until the Page Manager and Section Engine modules are built.') }}
        </p>
    </div>
</x-frontend-layout>
