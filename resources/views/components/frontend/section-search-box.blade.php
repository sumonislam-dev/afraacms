@props(['action', 'params' => [], 'placeholder' => null, 'name' => 'q', 'value' => null])

<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'mx-auto mb-10 max-w-md']) }}>
    @foreach ($params as $key => $paramValue)
        @foreach ((array) $paramValue as $v)
            <input type="hidden" name="{{ is_array($paramValue) ? $key.'[]' : $key }}" value="{{ $v }}">
        @endforeach
    @endforeach

    <div class="flex gap-2">
        <input
            type="search"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder ?? __('Search...') }}"
            class="block w-full rounded-full border-gray-300 shadow-xs focus:border-brand-500 focus:ring-brand-500"
        >
        <button type="submit" class="shrink-0 rounded-full bg-brand-500 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-600">
            {{ __('Search') }}
        </button>
    </div>
</form>
