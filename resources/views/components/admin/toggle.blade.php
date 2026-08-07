@props(['name', 'checked' => false, 'disabled' => false])

<label class="inline-flex items-center gap-3 {{ $disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
    <input type="hidden" name="{{ $name }}" value="0">

    <input
        {{ $attributes }}
        type="checkbox"
        name="{{ $name }}"
        value="1"
        class="peer sr-only"
        @checked($checked)
        @disabled($disabled)
    >

    <span class="relative h-6 w-11 shrink-0 rounded-full bg-gray-200 transition-colors duration-200 peer-checked:bg-indigo-600 after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform after:duration-200 peer-checked:after:translate-x-5"></span>
</label>
