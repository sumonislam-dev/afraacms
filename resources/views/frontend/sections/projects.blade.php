@php
    $projects = collect(app(\App\CMS\Services\ProjectService::class)->all())->take(6);
@endphp

@if ($projects->isNotEmpty())
    <section class="py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            @if ($section['heading'])
                <h2 class="text-center text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
            @endif

            @if ($section['subheading'])
                <p class="mt-2 text-center text-gray-600">{{ $section['subheading'] }}</p>
            @endif

            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-frontend.project-card :project="$project" />
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ $section['button_url'] ?: route('projects.index') }}" class="inline-block rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-500">
                    {{ $section['button_text'] ?: __('View All Projects') }}
                </a>
            </div>
        </div>
    </section>
@endif
