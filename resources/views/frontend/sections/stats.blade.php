<section class="py-16">
    <div class="mx-auto max-w-5xl px-4 sm:px-6">
        @if ($section['heading'])
            <h2 class="text-center text-2xl font-bold text-gray-900">{{ $section['heading'] }}</h2>
        @endif

        <div class="mt-10 grid grid-cols-2 gap-8 sm:grid-cols-4">
            @foreach ($section['items'] as $item)
                <x-frontend.counter :value="$item['value']" :label="$item['title']" :icon="$item['icon']" />
            @endforeach
        </div>
    </div>
</section>
