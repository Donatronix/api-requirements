<?php

declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use Illuminate\Support\Str;

/**
 * Trait UuidTrait
 */
trait UuidTrait
{
    /**
     * The "booting" method of the model.
     */
    public static function bootUuidTrait(): void
    {
        static::creating(static function ($model): void {
            $model->{$model->getKeyName()} = $model->{$model->getKeyName()} ?: (string) Str::orderedUuid();
        });
    }

    /**
     * Get the primary key for the model.
     */
    public function getKeyName(): string
    {
        return 'id';
    }

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @return false
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * The "type" of the auto-incrementing ID.
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
