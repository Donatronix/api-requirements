<?php

namespace App\Domain\Api\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $price = DB::table('prices')->where('product_id', $this->id)->first();
        if ($price) {
            $price = [
                'id' => $price->id,
                'original' => $price->original,
                'final' => $price->final,
                'discount_percentage' => $price->discount_percentage . '%',
                'currency' => $price->currency,
                'created_at' => $price->created_at,
                'updated_at' => $price->updated_at,
            ];
        }

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'category' => $this->category,
            'price' => $price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
