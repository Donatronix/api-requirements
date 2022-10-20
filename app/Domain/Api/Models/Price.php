<?php

namespace App\Domain\Api\Models;

use App\Domain\Shared\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Price.
 *
 * @package namespace App\Domain\Api\Models;
 */
class Price extends Model
{
    use HasFactory;
    use UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'original',
        'final',
        'discount_percentage',
        'currency',
    ];

    /**
     * The "booting" method of the model.
     */
    public static function boot(): void
    {
        static::creating(static function ($model): void {
            $discount = $model->original - $model->final;
            if ($discount > 0) {
                $model->discount_percentage = $discount . "%";
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Interact with the final price.
     *
     * @return Attribute
     */
    protected function final(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }

    /**
     * Interact with the original price.
     *
     * @return Attribute
     */
    protected function original(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }


}
