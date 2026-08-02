@props(['maxBatch' => 20])

<div x-data="mediaUploader({{ (int) $maxBatch }}, '{{ route('admin.media.store') }}')">
    <input type="file" id="media-upload-input" accept="image/*" multiple class="hidden" @change="stageFiles($event)">

    <x-primary-button type="button" onclick="document.getElementById('media-upload-input').click()" x-show="queue.length === 0">
        {{ __('Upload') }}
    </x-primary-button>

    <p x-show="limitError && queue.length === 0" x-text="limitError" class="mt-2 text-sm text-red-600" style="display: none;"></p>

    <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4" x-show="queue.length > 0" style="display: none;">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-gray-900" x-text="queue.length + ' file(s) selected'"></h3>

            <div class="flex items-center gap-3">
                <x-secondary-button type="button" @click="reset()" x-bind:disabled="uploading">
                    {{ __('Clear') }}
                </x-secondary-button>
                <x-primary-button type="button" @click="startUpload()" x-bind:disabled="uploading">
                    <span x-show="! uploading" x-text="'Upload ' + queue.length + ' file(s)'"></span>
                    <span x-show="uploading" style="display: none;">{{ __('Uploading...') }}</span>
                </x-primary-button>
            </div>
        </div>

        <div class="mt-4" x-show="uploading" style="display: none;">
            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                <div class="h-full rounded-full bg-indigo-600 transition-all duration-300" x-bind:style="'width: ' + progressPercent + '%'"></div>
            </div>
            <p class="mt-1 text-xs text-gray-500" x-text="completedCount + ' of ' + queue.length + ' uploaded'"></p>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6">
            <template x-for="(item, index) in queue" :key="item.id">
                <div class="relative overflow-hidden rounded-md border border-gray-200">
                    <img :src="item.previewUrl" class="aspect-square w-full object-cover" :class="{ 'opacity-40': item.status !== 'pending' }">

                    <div class="absolute inset-0 flex items-center justify-center" x-show="item.status === 'uploading'" style="display: none;">
                        <span class="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    </div>

                    <div class="absolute inset-0 flex items-center justify-center bg-green-600/70" x-show="item.status === 'done'" style="display: none;">
                        <x-admin.icon name="check-circle" class="h-6 w-6 text-white" />
                    </div>

                    <div class="absolute inset-0 flex items-center justify-center bg-red-600/70" x-show="item.status === 'error'" :title="item.error" style="display: none;">
                        <x-admin.icon name="x-circle" class="h-6 w-6 text-white" />
                    </div>

                    <button
                        type="button"
                        x-show="item.status === 'pending'"
                        @click="removeFromQueue(index)"
                        class="absolute right-1 top-1 rounded-full bg-white/90 p-0.5 text-gray-600 hover:text-red-600"
                        style="display: none;"
                    >
                        <x-admin.icon name="x-mark" class="h-3 w-3" />
                    </button>

                    <p class="truncate bg-white px-1 py-0.5 text-[10px] text-gray-600" x-text="item.file.name"></p>
                </div>
            </template>
        </div>

        <div class="mt-4 rounded-md bg-gray-50 p-3 text-sm text-gray-700" x-show="summary" x-text="summary" style="display: none;"></div>

        <div class="mt-4 flex justify-end" x-show="summary" style="display: none;">
            <x-secondary-button type="button" @click="window.location.reload()">{{ __('Refresh Library') }}</x-secondary-button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mediaUploader', (maxBatch, uploadUrl) => ({
            queue: [],
            uploading: false,
            completedCount: 0,
            limitError: '',
            summary: '',

            get progressPercent() {
                return this.queue.length ? Math.round((this.completedCount / this.queue.length) * 100) : 0;
            },

            stageFiles(event) {
                const files = Array.from(event.target.files);
                event.target.value = '';

                if (files.length === 0) {
                    return;
                }

                if (files.length > maxBatch) {
                    this.limitError = `You can upload up to ${maxBatch} files at a time. Please select fewer files.`;
                    return;
                }

                this.limitError = '';
                this.summary = '';
                this.completedCount = 0;
                this.queue = files.map((file, index) => ({
                    id: Date.now() + '-' + index,
                    file,
                    previewUrl: URL.createObjectURL(file),
                    status: 'pending',
                    error: null,
                }));
            },

            removeFromQueue(index) {
                URL.revokeObjectURL(this.queue[index].previewUrl);
                this.queue.splice(index, 1);
            },

            reset() {
                this.queue.forEach((item) => URL.revokeObjectURL(item.previewUrl));
                this.queue = [];
                this.completedCount = 0;
                this.limitError = '';
                this.summary = '';
            },

            async startUpload() {
                this.uploading = true;
                this.completedCount = 0;
                const failures = [];
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                for (const item of this.queue) {
                    item.status = 'uploading';

                    try {
                        const formData = new FormData();
                        formData.append('file', item.file);

                        const response = await fetch(uploadUrl, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: formData,
                        });

                        if (! response.ok) {
                            const data = await response.json().catch(() => ({}));
                            throw new Error(data.errors?.file?.[0] || 'Upload failed.');
                        }

                        item.status = 'done';
                    } catch (error) {
                        item.status = 'error';
                        item.error = error.message;
                        failures.push(item.file.name + ': ' + error.message);
                    }

                    this.completedCount++;
                }

                this.uploading = false;
                this.summary = failures.length > 0
                    ? (this.queue.length - failures.length) + ' of ' + this.queue.length + ' uploaded. Failed - ' + failures.join(', ')
                    : 'All ' + this.queue.length + ' file(s) uploaded successfully.';
            },
        }));
    });
</script>
