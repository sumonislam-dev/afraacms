<section class="py-16">
    <div class="mx-auto max-w-5xl px-4 sm:px-6">
        @if ($section['heading'])
            <h2 class="text-center text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
        @endif

        <div class="mt-10 grid grid-cols-2 gap-8 text-center sm:grid-cols-4">
            @foreach ($section['items'] as $item)
                <div>
                    @if ($item['icon'])
                        <x-icon :name="$item['icon']" class="mx-auto h-8 w-8 text-indigo-600" />
                    @endif

                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $item['value'] }}</p>

                    @if ($item['title'])
                        <p class="mt-1 text-sm text-gray-500">{{ $item['title'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
