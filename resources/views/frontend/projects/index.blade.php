<x-frontend-layout :title="__('Projects')">
    <x-banner type="page" :page-title="__('Projects')" />

    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        @if ($categories->isNotEmpty())
            <div class="flex flex-wrap justify-center gap-2">
                <a
                    href="{{ route('projects.index') }}"
                    class="rounded-full px-3 py-1 text-sm font-medium {{ request('category') ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-brand-500 text-white' }}"
                >
                    {{ __('All') }}
                </a>

                @foreach ($categories as $category)
                    <a
                        href="{{ route('projects.index', ['category' => $category->slug]) }}"
                        class="rounded-full px-3 py-1 text-sm font-medium {{ request('category') === $category->slug ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if (empty($projects))
            <p class="mt-10 text-center text-gray-500">{{ __('No projects yet.') }}</p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-frontend.project-card :project="$project" />
                @endforeach
            </div>
        @endif
    </div>
</x-frontend-layout>
