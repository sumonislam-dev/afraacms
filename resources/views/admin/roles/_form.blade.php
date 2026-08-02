@php
    $isEdit = isset($role);
@endphp

<x-admin.form-section
    :title="$isEdit ? __('Role Details') : __('New Role')"
    :description="__('Name the role and choose which permissions it grants.')"
>
    <div>
        <x-input-label for="name" :value="__('Role Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $role->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label :value="__('Permissions')" />

        <div class="mt-2 space-y-4">
            @foreach ($permissionsByModule as $module => $modulePermissions)
                <div class="rounded-md border border-gray-200 p-3">
                    <p class="text-sm font-semibold capitalize text-gray-700">{{ $module }}</p>
                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-2">
                        @foreach ($modulePermissions as $permission)
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    @checked(collect(old('permissions', $assigned))->contains($permission->name))
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                >
                                {{ \Illuminate\Support\Str::after($permission->name, '.') }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('permissions')" />
    </div>

    <x-slot name="actions">
        <x-secondary-button type="button" onclick="window.location='{{ route('admin.roles.index') }}'">{{ __('Cancel') }}</x-secondary-button>
        <x-primary-button>{{ $isEdit ? __('Update Role') : __('Create Role') }}</x-primary-button>
    </x-slot>
</x-admin.form-section>
