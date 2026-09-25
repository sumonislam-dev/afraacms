<x-admin-layout :breadcrumbs="[['label' => __('Enrollments'), 'url' => route('admin.enrollments.index')], ['label' => __('Import')]]">
    <x-slot name="title">{{ __('Import Enrollments') }}</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900">{{ __('Import Enrollments') }}</h2>
    </x-slot>

    <x-admin.card>
        <div class="space-y-4">
            <p class="text-sm text-gray-600">
                {{ __('Upload an .xlsx or .csv file to enroll many students into courses at once. Students and courses are matched by their Student Code / Course Code (shown on their own lists).') }}
                <a href="{{ route('admin.enrollments.import.template') }}" class="font-medium text-indigo-600 hover:text-indigo-900">{{ __('Download the template') }}</a>
                {{ __('to see the exact columns expected.') }}
            </p>

            @if (session('import_failures'))
                <div class="rounded-md border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">
                        {{ __(':count row(s) could not be imported:', ['count' => count(session('import_failures'))]) }}
                    </p>
                    <ul class="mt-2 max-h-64 space-y-1 overflow-y-auto text-sm text-red-700">
                        @foreach (session('import_failures') as $failure)
                            <li>
                                {{ __('Row :row (:attribute): :errors', [
                                    'row' => $failure['row'],
                                    'attribute' => $failure['attribute'],
                                    'errors' => implode(' ', $failure['errors']),
                                ]) }}
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-xs text-red-600">{{ __('Every other row in the file was imported successfully. Fix these rows and re-upload just the corrections.') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.enrollments.import.process') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="file" :value="__('Spreadsheet')" />
                    <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv" required class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <x-secondary-button type="button" onclick="window.location='{{ route('admin.enrollments.index') }}'">{{ __('Cancel') }}</x-secondary-button>
                    <x-primary-button>{{ __('Upload & Import') }}</x-primary-button>
                </div>
            </form>
        </div>
    </x-admin.card>
</x-admin-layout>
