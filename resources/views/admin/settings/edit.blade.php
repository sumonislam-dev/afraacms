<x-admin-layout :breadcrumbs="[['label' => __('Settings')]]">
    <x-slot name="title">{{ __('Settings') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Settings') }}</h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Global configuration for the site. Changes take effect everywhere immediately after saving.') }}
        </p>
    </x-slot>

    @unless ($canEdit)
        <div class="mb-6 rounded-md bg-amber-50 p-4 text-sm font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">
            {{ __('You have view-only access to Settings. Only a Super Admin can make changes here.') }}
        </div>
    @endunless

    <div class="lg:grid lg:grid-cols-4 lg:gap-6" x-data="{ tab: '{{ array_key_first($groups) }}' }">
        <nav class="flex gap-1 overflow-x-auto pb-2 lg:col-span-1 lg:flex-col lg:overflow-visible lg:pb-0">
            @foreach ($groups as $groupKey => $group)
                <button
                    type="button"
                    @click="tab = '{{ $groupKey }}'"
                    :class="tab === '{{ $groupKey }}' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex flex-shrink-0 items-center gap-3 rounded-md px-3 py-2 text-sm font-medium whitespace-nowrap"
                >
                    <x-admin.icon :name="$group['icon']" class="h-5 w-5" />
                    {{ $group['label'] }}
                </button>
            @endforeach
        </nav>

        <div class="mt-6 lg:col-span-3 lg:mt-0">
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @foreach ($groups as $groupKey => $group)
                    <div x-show="tab === '{{ $groupKey }}'">
                        <x-admin.card :title="$group['label']">
                            <div class="space-y-6">
                                @foreach ($group['fields'] as $fieldKey => $field)
                                    @include('admin.settings._field', ['key' => $fieldKey, 'field' => $field, 'value' => $values[$fieldKey] ?? null])
                                @endforeach
                            </div>
                        </x-admin.card>
                    </div>
                @endforeach

                @if ($canEdit)
                    <div class="mt-6 flex justify-end">
                        <x-primary-button>{{ __('Save Settings') }}</x-primary-button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-admin-layout>
