<?php

namespace Database\Seeders;

use App\Domain\Api\Models\Product;
use App\Domain\Api\Services\Interfaces\ProductServiceInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Throwable;

class PriceSeeder extends Seeder
{
    public function __construct(protected ProductServiceInterface $products)
    {
    }

    protected array $prices = [
        [
            "original" => 89000,
            "final" => 89000,
        ],
        [
            "original" => 99000,
            "final" => 99000,
        ],
        [
            "original" => 150000,
            "final" => 150000,
        ],
        [
            "original" => 20000,
            "final" => 20000,
        ],
        [
            "original" => 250000,
            "final" => 250000,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        $products = Product::all()->toArray();
        foreach ($this->prices as $key => $price) {
            $this->products->update(array_merge($products[$key], array_merge($price, [
                'id' => (string)Str::orderedUuid(),
                'product_id' => $products[$key],
            ])), [
                $products[$key]['id'],
            ]);

        }
    }
}
