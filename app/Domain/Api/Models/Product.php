<?php

namespace App\Domain\Api\Models;

use App\Domain\Shared\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Product.
 *
 * @package namespace App\Domain\Api\Models;
 */
class Product extends Model
{
    use HasFactory;
    use UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'name',
        'category',
    ];

    /**
     * @return HasOne
     */
    public function price(): HasOne
    {
        return $this->hasOne(Price::class);
    }

}
