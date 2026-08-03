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
                    <a href="{{ route('projects.show', $project['slug']) }}" class="group block overflow-hidden rounded-lg border border-gray-200">
                        <div class="relative">
                            @if ($project['cover_image_url'])
                                <img src="{{ $project['cover_image_url'] }}" alt="" class="aspect-video w-full object-cover transition group-hover:opacity-90">
                            @else
                                <div class="flex aspect-video w-full items-center justify-center bg-gray-100 text-gray-300">
                                    <x-icon name="photo" class="h-10 w-10" />
                                </div>
                            @endif

                            @if ($project['is_featured'])
                                <span class="absolute left-2 top-2 rounded-full bg-amber-500 px-2 py-0.5 text-xs font-semibold text-white">{{ __('Featured') }}</span>
                            @endif
                        </div>

                        <div class="p-4">
                            @if ($project['category'])
                                <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">{{ $project['category']['name'] }}</p>
                            @endif

                            <h2 class="mt-1 font-semibold text-gray-900">{{ $project['title'] }}</h2>

                            @if ($project['excerpt'])
                                <p class="mt-1 truncate text-sm text-gray-500">{{ $project['excerpt'] }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-frontend-layout>
