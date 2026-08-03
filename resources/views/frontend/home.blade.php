<x-frontend-layout>
    <x-banner type="homepage" />

    <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6">
        <h1 class="text-3xl font-bold">
            {{ setting('tagline') ?: __('Welcome to :name', ['name' => setting('site_name', config('app.name', 'AfraaCMS'))]) }}
        </h1>

        @can('update', \App\Models\Setting::class)
            <p class="mt-4 text-gray-600">
                {{ __('No homepage has been set yet.') }}
                <a href="{{ route('admin.settings.edit') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    {{ __('Choose one in Settings.') }}
                </a>
            </p>
        @endcan
    </div>
</x-frontend-layout>
