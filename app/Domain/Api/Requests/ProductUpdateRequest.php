<?php

namespace App\Domain\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sku' => 'required|string',
            'name' => 'required|string',
            'category' => 'required|string',
            'original' => 'required|numeric',
            'final' => 'required|numeric',
            'currency' => 'required|string',
        ];
    }
}
