<?php

namespace Tests\Feature;

use App\Domain\Api\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that index returns data.
     *
     * @return void
     */
    public function test_index_returns_data(): void
    {
        $response = $this->get('api/products');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'sku',
                    'name',
                    'category',
                    'price' => [
                        'id',
                        'product_id',
                        'original',
                        'final',
                        'discount_percentage',
                        'currency',
                        'created_at',
                        'updated_at',
                    ],
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * Test that product is stored successfully.
     *
     * @return void
     */
    public function test_if_product_is_stored_successfully(): void
    {
        $payload = [
            "sku" => "000006",
            "name" => "BMW Compact Car X3",
            "category" => "vehicle",
            "original" => 250000,
            "final" => 250000,
        ];
        $this->json('post', 'api/products', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'sku',
                        'name',
                        'category',
                        'price' => [
                            'id',
                            'product_id',
                            'original',
                            'final',
                            'discount_percentage',
                            'currency',
                            'created_at',
                            'updated_at',
                        ],
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
        $this->assertDatabaseHas('products', $payload);
    }

    /**
     * Test that product is shown successfully.
     *
     * @return void
     */
    public function test_product_is_shown_correctly(): void
    {
        $product = Product::create([
            "sku" => "000008",
            "name" => "Hyundai Convertible X2, Electric",
            "category" => "vehicle",
        ]);
        DB::table('prices')->insert([
            "original" => 350000,
            "final" => 250000,
            'product_id' => $product->id,
            'id' => (string)Str::orderedUuid(),
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);

        $this->json('get', "api/products/$product->id")
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'category' => $product->category,
                        'created_at' => (string)$product->created_at,
                        'updated_at' => (string)$product->updated_at,
                        'price' => [
                            'id' => $product->price->id,
                            'product_id' => $product->price->product_id,
                            'original' => $product->price->original,
                            'final' => $product->price->final,
                            'discount_percentage' => $product->price->discount_percentage,
                            'currency' => $product->price->currency,
                            'created_at' => (string)$product->price->created_at,
                            'updated_at' => (string)$product->price->updated_at,
                        ],
                    ],
                ]
            );
    }

    /**
     * @return void
     */
    public function test_product_is_destroyed(): void
    {

        $product = Product::create([
            "sku" => "000032",
            "name" => "Nissan X2, Electric",
            "category" => "vehicle",
        ]);
        DB::table('prices')->insert([
            "original" => 350000,
            "final" => 250000,
            'product_id' => $product->id,
            'id' => (string)Str::orderedUuid(),
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);

        $this->json('delete', "api/products/$product->id")
            ->assertNoContent();
        $this->assertDatabaseMissing('products', $product);
    }

    /**
     * Test that product is updated successfully.
     *
     * @return void
     */
    public function test_update_product_returns_correct_data(): void
    {
        $product = Product::create([
            "sku" => "000040",
            "name" => "J5 Convertible X2, Electric",
            "category" => "vehicle",
        ]);
        DB::table('prices')->insert([
            "original" => 400000,
            "final" => 250000,
            'product_id' => $product->id,
            'id' => (string)Str::orderedUuid(),
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);

        $payload = [
            "name" => "Volvo Convertible X2, Electric",
            "category" => "vehicle",
        ];

        $this->json('put', "api/products/$product->id", $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $product->id,
                        'name' => $payload['name'],
                        'category' => $payload['category'],
                        'created_at' => (string)$product->created_at,
                        'price' => [
                            'id' => $product->price->id,
                            'discount_percentage' => $product->price->discount_percentage,
                        ],
                    ],
                ]
            );
    }

    /**
     * @return void
     */
    public function test_show_for_missing_product(): void
    {

        $this->json('get', "api/products/0")
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'status',
                'message',
            ]);

    }

    /**
     * @return void
     */
    public function test_store_with_missing_data(): void
    {

        $payload = [
            "sku" => "000028",
            "name" => "Mack Convertible X2, Electric",
            "category" => "vehicle",
            //price is missing
        ];
        $this->json('post', 'api/products', $payload)
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['status']);
    }
}
