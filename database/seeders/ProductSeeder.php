<?php

namespace Database\Seeders;

use App\Domain\Api\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    protected array $products= [
      [
        "sku"=> "000001",
        "name"=> "Full coverage insurance",
        "category"=> "insurance",
      ],
      [
        "sku"=> "000002",
        "name"=> "Compact Car X3",
        "category"=> "vehicle",
      ],
      [
        "sku"=> "000003",
        "name"=> "SUV Vehicle, high end",
        "category"=> "vehicle",
      ],
      [
        "sku"=> "000004",
        "name"=> "Basic coverage",
        "category"=> "insurance",
      ],
      [
        "sku"=> "000005",
        "name"=> "Convertible X2, Electric",
        "category"=> "vehicle",
      ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       foreach($this->products as $product){
           Product::query()->create($product);
       }
    }
}
