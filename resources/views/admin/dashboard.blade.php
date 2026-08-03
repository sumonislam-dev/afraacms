<x-admin-layout>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Dashboard') }}</h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Welcome back, :name. Here\'s an overview of your site.', ['name' => Auth::user()->name]) }}
        </p>
    </x-slot>

    @if ($cards->isNotEmpty())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($cards as $card)
                <a href="{{ Route::has($card['route']) ? route($card['route']) : '#' }}">
                    <x-admin.card class="h-full transition-shadow hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $card['value'] }}</p>
                                @if (! empty($card['sub']))
                                    <p class="mt-1 text-sm {{ ! empty($card['alert']) ? 'font-medium text-amber-600' : 'text-gray-500' }}">
                                        {{ $card['sub'] }}
                                    </p>
                                @endif
                            </div>
                            <span class="flex-shrink-0 rounded-lg p-2 {{ ! empty($card['alert']) ? 'bg-amber-50 text-amber-500' : 'bg-indigo-50 text-indigo-500' }}">
                                <x-admin.icon :name="$card['icon']" class="h-6 w-6" />
                            </span>
                        </div>
                    </x-admin.card>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        @can('activity.view')
            <div class="lg:col-span-2">
                <x-admin.card :title="__('Recent Activity')">
                    <x-slot name="header">
                        <a href="{{ route('admin.activity.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            {{ __('View all') }} &rarr;
                        </a>
                    </x-slot>

                    @if ($recentActivity->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No activity recorded yet.') }}</p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($recentActivity as $activity)
                                <li class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm text-gray-900">
                                            <span class="font-medium">{{ $activity->causer?->name ?? __('System') }}</span>
                                            {{ $activity->description }}
                                            <span class="text-gray-500">{{ class_basename($activity->subject_type) }}</span>
                                            @if ($label = \App\Support\ActivitySubject::label($activity))
                                                <span class="text-gray-500">&mdash; {{ $label }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <time
                                        class="flex-shrink-0 text-xs text-gray-400"
                                        title="{{ $activity->created_at->format('M j, Y g:i A') }}"
                                    >
                                        {{ $activity->created_at->diffForHumans() }}
                                    </time>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-admin.card>
            </div>
        @endcan

        @if ($quickActions->isNotEmpty())
            <div>
                <x-admin.card :title="__('Quick Actions')">
                    <div class="space-y-2">
                        @foreach ($quickActions as $action)
                            <a
                                href="{{ route($action['route']) }}"
                                class="flex items-center gap-3 rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                <x-admin.icon :name="$action['icon']" class="h-5 w-5 text-gray-400" />
                                {{ $action['label'] }}
                            </a>
                        @endforeach
                    </div>
                </x-admin.card>
            </div>
        @endif
    </div>
</x-admin-layout>
