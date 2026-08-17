<?php

namespace App\CMS\Services\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Generic create/update/delete/restore for services whose write operations
 * are plain Eloquent CRUD with no extra side effects (identifier generation,
 * relation syncing, cache invalidation, etc.) - those stay hand-written on
 * the service. forceDelete is deliberately not included here since every
 * current user of this trait guards it against a dependent relation first.
 */
trait PerformsBasicCrud
{
    /**
     * Fully-qualified class name of the model this service manages.
     */
    abstract protected function modelClass(): string;

    public function create(array $data): Model
    {
        return $this->modelClass()::create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model;
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }

    public function restore(Model $model): Model
    {
        $model->restore();

        return $model;
    }
}
