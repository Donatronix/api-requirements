<?php

namespace Database\Seeders;

use App\Domain\Api\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PriceSeeder extends Seeder
{
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
     */
    public function run()
    {
        $products = Product::all()->pluck('id')->toArray();
        foreach ($this->prices as $key => $price) {
            $val = array_merge($price, [
                'id' => (string)Str::orderedUuid(),
                'product_id' => $products[$key],
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
            DB::table('prices')->insert($val);

        }
    }
}
