<?php

declare(strict_types=1);

namespace App\Domain\Api\Services\Interfaces;

use App\Domain\Shared\Services\Interfaces\BaseServiceInterface;

/**
 * Class ProductServiceInterface.
 *
 * @package namespace App\Domain\Api\Services\Interfaces;
 */
interface ProductServiceInterface extends BaseServiceInterface
{
    /**
     * @param string $category
     *
     * @return mixed
     */
    public function getProductsByCategory(string $category): mixed;

    public function getProductsWithOutDiscount();
}
