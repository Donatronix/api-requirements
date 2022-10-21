<?php

namespace Database\Seeders;

use App\Domain\Api\Services\Interfaces\ProductServiceInterface;
use Illuminate\Database\Seeder;
use Throwable;

class ProductSeeder extends Seeder
{
    public function __construct(protected ProductServiceInterface $services)
    {
    }

    protected array $products = [
        [
            "sku" => "000001",
            "name" => "Full coverage insurance",
            "category" => "insurance",
            "original" => 89000,
            "final" => 89000,
        ],
        [
            "sku" => "000002",
            "name" => "Compact Car X3",
            "category" => "vehicle",
            "original" => 99000,
            "final" => 99000,
        ],
        [
            "sku" => "000003",
            "name" => "SUV Vehicle, high end",
            "category" => "vehicle",
            "original" => 150000,
            "final" => 150000,
        ],
        [
            "sku" => "000004",
            "name" => "Basic coverage",
            "category" => "insurance",
            "original" => 20000,
            "final" => 20000,
        ],
        [
            "sku" => "000005",
            "name" => "Convertible X2, Electric",
            "category" => "vehicle",
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
        array_map(function ($product) {
            $this->services->store($product);
        }, $this->products);


    }
}
