<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-lg bg-white shadow ring-1 ring-black/5']) }}>
    <table class="min-w-full divide-y divide-gray-200">
        {{ $slot }}
    </table>
</div>
