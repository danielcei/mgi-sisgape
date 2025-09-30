<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SafeDeploy\Laravel\Exceptions\DefaultUserModelNotFound;
use SafeDeploy\Laravel\Observers\UserstampsObserver;
use SafeDeploy\SafeDeploy;

/**
 * @mixin Model
 */
trait Userstamps
{
    public static function bootUserstamps(): void
    {
        self::observe(UserstampsObserver::class);
    }

    /**
     * @return BelongsTo<Model, $this>
     *
     * @throws DefaultUserModelNotFound
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(SafeDeploy::defaultUserModel(), 'created_by');
    }

    /**
     * @return BelongsTo<Model, $this>
     *
     * @throws DefaultUserModelNotFound
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(SafeDeploy::defaultUserModel(), 'deleted_by');
    }

    public function setCreatedBy(null|int|string $userId): self
    {
        $this->setAttribute('created_by', $userId);

        return $this;
    }

    public function setDeletedBy(null|int|string $userId): self
    {
        $this->setAttribute('deleted_by', $userId);
        // When deleting, we need to force a save, because of how the delete method works.
        $this->save();

        return $this;
    }

    public function setUpdatedBy(null|int|string $userId): self
    {
        $this->setAttribute('updated_by', $userId);

        return $this;
    }

    /**
     * @return BelongsTo<Model, $this>
     *
     * @throws DefaultUserModelNotFound
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(SafeDeploy::defaultUserModel(), 'updated_by');
    }
}
