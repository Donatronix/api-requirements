<?php

namespace App\Domain\Api\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'original' => $this->original,
            'final' => $this->final,
            'discount_percentage' => $this->discount_percentage,
            'currency' => $this->currency,
        ];
    }
}
