<x-admin-layout :breadcrumbs="[['label' => __('Inbox')]]">
    <x-slot name="title">{{ __('Inbox') }}</x-slot>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ __('Inbox') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Messages submitted through the site\'s contact form.') }}</p>
        </div>
    </x-slot>

    <x-admin.table>
        <thead>
            <tr>
                <x-admin.table-th><span class="sr-only">{{ __('Status') }}</span></x-admin.table-th>
                <x-admin.table-th>{{ __('From') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Subject') }}</x-admin.table-th>
                <x-admin.table-th>{{ __('Received') }}</x-admin.table-th>
                <x-admin.table-th><span class="sr-only">{{ __('Actions') }}</span></x-admin.table-th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($messages as $message)
                <tr class="{{ $message->is_read ? '' : 'bg-indigo-50/40' }}">
                    <x-admin.table-td>
                        @unless ($message->is_read)
                            <span class="inline-block h-2 w-2 rounded-full bg-indigo-600" title="{{ __('Unread') }}"></span>
                        @endunless
                    </x-admin.table-td>
                    <x-admin.table-td class="{{ $message->is_read ? 'text-gray-900' : 'font-semibold text-gray-900' }}">
                        {{ $message->name }}
                        <span class="block text-xs font-normal text-gray-500">{{ $message->email }}</span>
                    </x-admin.table-td>
                    <x-admin.table-td class="max-w-xs truncate">{{ $message->subject ?: __('(no subject)') }}</x-admin.table-td>
                    <x-admin.table-td class="text-sm text-gray-500">{{ $message->created_at->format('M j, Y g:i A') }}</x-admin.table-td>
                    <x-admin.table-td>
                        <div class="flex items-center justify-end gap-3">
                            @can('view', $message)
                                <a href="{{ route('admin.contact.show', $message) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                            @endcan

                            @can('delete', $message)
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'delete-message-{{ $message->id }}')" class="text-sm font-medium text-red-600 hover:text-red-900">
                                    {{ __('Delete') }}
                                </button>

                                <x-modal :name="'delete-message-'.$message->id">
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900">{{ __('Delete this message?') }}</h2>
                                        <p class="mt-1 text-sm text-gray-600">{{ __('This action cannot be undone.') }}</p>

                                        <form method="POST" action="{{ route('admin.contact.destroy', $message) }}" class="mt-6 flex justify-end gap-3">
                                            @csrf
                                            @method('DELETE')
                                            <x-secondary-button type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                            <x-danger-button>{{ __('Delete') }}</x-danger-button>
                                        </form>
                                    </div>
                                </x-modal>
                            @endcan
                        </div>
                    </x-admin.table-td>
                </tr>
            @empty
                <tr>
                    <x-admin.table-td colspan="5" class="text-center text-gray-500">{{ __('No messages yet.') }}</x-admin.table-td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table>

    <x-admin.pagination :paginator="$messages" />
</x-admin-layout>
