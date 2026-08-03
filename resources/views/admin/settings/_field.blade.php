@php
    $id = 'setting_'.$key;
    $currentValue = old($key, $value);
@endphp

<div>
    <x-input-label :for="$id" :value="$field['label']" />

    @switch($field['type'])
        @case('textarea')
            <x-textarea :id="$id" name="{{ $key }}" class="mt-1 block w-full" rows="3" :disabled="! $canEdit">{{ $currentValue }}</x-textarea>
            @break

        @case('boolean')
            <div class="mt-2">
                <x-admin.toggle :id="$id" name="{{ $key }}" :checked="(bool) $currentValue" :disabled="! $canEdit" />
            </div>
            @break

        @case('image')
            <x-admin.media-picker name="{{ $key }}" :current="$currentValue" :disabled="! $canEdit" />
            @break

        @case('color')
            <x-admin.color-input :id="$id" name="{{ $key }}" :value="$currentValue" :disabled="! $canEdit" />
            @break

        @case('select')
            <select id="{{ $id }}" name="{{ $key }}" @disabled(! $canEdit) class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach ($field['resolved_options'] ?? [] as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string) $currentValue === (string) $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            @break

        @case('password')
            <x-text-input :id="$id" name="{{ $key }}" type="password" class="mt-1 block w-full" autocomplete="new-password" :disabled="! $canEdit" />
            <p class="mt-1 text-sm text-gray-500">{{ __('Leave blank to keep the current value.') }}</p>
            @break

        @default
            <x-text-input :id="$id" name="{{ $key }}" :type="$field['type']" class="mt-1 block w-full" :value="$currentValue" :disabled="! $canEdit" />
    @endswitch

    @if (! empty($field['description']))
        <p class="mt-1 text-sm text-gray-500">{{ $field['description'] }}</p>
    @endif

    <x-input-error class="mt-2" :messages="$errors->get($key)" />
</div>
