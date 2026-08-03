<x-frontend-layout :title="__('Projects')">
    <x-banner type="page" />

    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <h1 class="text-center text-3xl font-bold text-gray-900">{{ __('Projects') }}</h1>

        @if ($categories->isNotEmpty())
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <a
                    href="{{ route('projects.index') }}"
                    class="rounded-full px-3 py-1 text-sm font-medium {{ request('category') ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-indigo-600 text-white' }}"
                >
                    {{ __('All') }}
                </a>

                @foreach ($categories as $category)
                    <a
                        href="{{ route('projects.index', ['category' => $category->slug]) }}"
                        class="rounded-full px-3 py-1 text-sm font-medium {{ request('category') === $category->slug ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if (empty($projects))
            <p class="mt-10 text-center text-gray-500">{{ __('No projects yet.') }}</p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-frontend.project-card :project="$project" />
                @endforeach
            </div>
        @endif
    </div>
</x-frontend-layout>
