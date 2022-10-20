<?php

namespace App\Domain\Api\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class ProductValidator.
 *
 * @package namespace App\Domain\Api\Validators;
 */
class ProductValidator extends LaravelValidator
{
    /**
     * Validation Rules
     *
     * @var array
     */
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'sku' => 'required|string|unique:products',
            'name' => 'required|string|unique:products',
            'category' => 'required|string',
            'original' => 'required|numeric',
            'final' => 'required|numeric',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'sku' => 'required|string',
            'name' => 'required|string',
            'category' => 'required|string',
            'original' => 'required|numeric',
            'final' => 'required|numeric',
        ],
    ];
}
