<?php

namespace App\CMS\Services;

use App\Models\MediaItem;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MediaService
{
    /**
     * Upload a new file into the media library.
     */
    public function upload(UploadedFile $file, ?User $uploader = null, ?string $title = null): MediaItem
    {
        $item = MediaItem::create([
            'title' => $title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'uploaded_by' => $uploader?->id,
        ]);

        $item->addMedia($file)
            ->withCustomProperties($this->dimensions($file))
            ->toMediaCollection('file');

        return $item;
    }

    /**
     * Replace an existing item's file, keeping its title and identity.
     */
    public function replace(MediaItem $item, UploadedFile $file): MediaItem
    {
        $item->clearMediaCollection('file');

        $item->addMedia($file)
            ->withCustomProperties($this->dimensions($file))
            ->toMediaCollection('file');

        return $item->fresh();
    }

    /**
     * Rename an existing item.
     */
    public function rename(MediaItem $item, string $title): MediaItem
    {
        $item->update(['title' => $title]);

        return $item;
    }

    /**
     * Delete an item and its underlying file(s).
     */
    public function delete(MediaItem $item): void
    {
        $item->delete();
    }

    /**
     * Search the library, newest uploads first.
     */
    public function search(?string $term, int $perPage = 24): LengthAwarePaginator
    {
        return MediaItem::query()
            ->with('media')
            ->search($term)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Read an uploaded image's pixel dimensions via Intervention Image.
     *
     * @return array<string, int>
     */
    private function dimensions(UploadedFile $file): array
    {
        $image = ImageManager::usingDriver(Driver::class)->decodePath($file->getRealPath());

        return [
            'width' => $image->width(),
            'height' => $image->height(),
        ];
    }
}
