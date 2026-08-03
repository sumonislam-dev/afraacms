<x-admin-layout :breadcrumbs="[['label' => __('Inbox'), 'url' => route('admin.contact.index')], ['label' => $message->subject ?: $message->name]]">
    <x-slot name="title">{{ __('Message') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ $message->subject ?: __('(no subject)') }}</h2>
    </x-slot>

    <x-admin.card>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('From') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $message->name }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    <a href="mailto:{{ $message->email }}" class="text-indigo-600 hover:text-indigo-500">{{ $message->email }}</a>
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('Received') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $message->created_at->format('M j, Y g:i A') }}</dd>
            </div>

            @if ($message->ip_address)
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('IP Address') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $message->ip_address }}</dd>
                </div>
            @endif
        </dl>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <dt class="text-sm font-medium text-gray-500">{{ __('Message') }}</dt>
            <dd class="mt-2 whitespace-pre-line text-sm text-gray-900">{{ $message->message }}</dd>
        </div>

        <div class="mt-6 flex justify-between border-t border-gray-100 pt-6">
            <a href="{{ route('admin.contact.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">&larr; {{ __('Back to Inbox') }}</a>

            <a href="mailto:{{ $message->email }}?subject={{ urlencode('Re: '.($message->subject ?: __('Your message'))) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                {{ __('Reply by Email') }}
            </a>
        </div>
    </x-admin.card>
</x-admin-layout>
