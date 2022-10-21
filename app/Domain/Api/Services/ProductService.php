<?php

declare(strict_types=1);

namespace App\Domain\Api\Services;


use App\Domain\Api\Models\Price;
use App\Domain\Api\Models\Product;
use App\Domain\Api\Repositories\Contracts\PriceRepository;
use App\Domain\Api\Repositories\Contracts\ProductRepository;
use App\Domain\Api\Services\Interfaces\ProductServiceInterface;
use App\Domain\Api\Validators\ProductValidator;
use App\Domain\Shared\Services\BaseService;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Class ProductService.
 *
 * @package namespace App\Domain\Api\Services;
 */
class ProductService extends BaseService implements ProductServiceInterface
{

    /**
     * @param ProductRepository $repository
     * @param PriceRepository   $prices
     * @param ProductValidator  $validator
     */
    public function __construct(
        protected ProductRepository $repository,
        protected PriceRepository   $prices,
        protected ProductValidator  $validator
    )
    {
        //
    }

    /**
     * @return ProductRepository
     */
    public function getRepository(): ProductRepository
    {
        return $this->repository;
    }

    /**
     * @return ProductValidator
     */
    public function getValidator(): ProductValidator
    {
        return $this->validator;
    }

    /**
     * Get products by category
     *
     * @param string $category
     *
     * @return mixed
     */
    public function getProductsByCategory(string $category): mixed
    {
        return $this->repository->findWhere([
            ['category', 'like', '%' . $category . '%'],
        ]);
    }

    /**
     * Get products by price
     *
     * @param $price
     *
     * @return mixed
     */
    public function getProductsByPrice($price): mixed
    {
        return $this->repository->scopeQuery(function ($query) use ($price) {
            return $query->whereHas('prices', function ($q) use ($price) {
                return $q->where('final', $price)
                    ->orWhere('original', $price);
            });
        })->all();
    }

    /**
     * Get products with discount
     *
     * @return mixed
     */
    public function getProductsWithOutDiscount(): mixed
    {
        return $this->repository->scopeQuery(function ($query) {
            return $query->whereHas('prices', function ($q) {
                return $q->whereNull('discount_percentage');
            });
        })->all();
    }

    public function store(array $request): mixed
    {
        try {
            DB::beginTransaction();

            $product = Product::create([
                'sku' => $request['sku'],
                'name' => $request['name'],
                'category' => $request['category'],
            ]);

            if (strtolower($request['category']) === 'insurance') {
                $discount_percentage = '30%';
                $final = $request['original'] - ($request['original'] * 0.3);
            }

            if (!isset($discount_percentage)) {
                $discount_percentage = ($request['original'] - $request['final']) * 100 / $request['original'];
                if ($discount_percentage <= 0) {
                    $discount_percentage = null;
                }
            }

            if ($request['sku'] === '000003') {
                $discount_percentage = '15%';
                $final = $request['original'] - ($request['original'] * 0.15);
            }

            Price::create([
                'product_id' => $product->id,
                'original' => $request['original'],
                'final' => $final ?? $request['final'],
                'discount_percentage' => $discount_percentage ?? null,
            ]);

            DB::commit();

            return $product;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new RuntimeException($e->getMessage());
        }

    }

    public function update(array $request, $id): mixed
    {
        try {
            DB::beginTransaction();

            $product = $this->repository->find($id);
            $sku = $request['sku'];
            $name = $request['name'];

            if ($sku !== $product->sku && $this->repository->findWhere([['sku', 'like', "%$sku%"],])->first() !== null) {
                throw new RuntimeException('SKU already exists for another product');
            }

            if ($name !== $product->name && $this->repository->findWhere([['name', 'like', "%$name%"],])->first() !== null) {
                throw new RuntimeException('Name already exists for another product');
            }

            Product::query()->update([
                'sku' => $request['sku'],
                'name' => $request['name'],
                'category' => $request['category'],
            ], $id);

            if (strtolower($request['category']) === 'insurance') {
                $discount_percentage = '30%';
                $final = $request['original'] - ($request['original'] * 0.3);
            }

            if (!isset($discount_percentage)) {
                $discount_percentage = ($request['original'] - $request['final']) * 100 / $request['original'];
                if ($discount_percentage <= 0) {
                    $discount_percentage = null;
                }
            }

            if ($request['sku'] === '000003') {
                $discount_percentage = '15%';
                $final = $request['original'] - ($request['original'] * 0.15);
            }

            $product->price()->update([
                'original' => $request['original'],
                'final' => $final ?? $request['final'],
                'discount_percentage' => $discount_percentage ?? null,
            ]);

            DB::commit();

            return $this->repository->find($id);
        } catch (Throwable $e) {
            DB::rollBack();
            throw new RuntimeException($e->getMessage());
        }

    }

    /**
     * @param $id
     *
     * @return int|null
     *
     * @throws Throwable
     */
    public function delete($id): ?int
    {
        try {
            DB::beginTransaction();

            DB::table('prices')
                ->where('product_id', $id)
                ->delete();

            $product = parent::delete($id);

            DB::commit();

            return $product;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new RuntimeException($e->getMessage());
        }

    }
}
