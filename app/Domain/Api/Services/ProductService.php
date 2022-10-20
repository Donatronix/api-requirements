<?php

declare(strict_types=1);

namespace App\Domain\Api\Services;


use App\Domain\Api\Repositories\Contracts\PriceRepository;
use App\Domain\Api\Repositories\Contracts\ProductRepository;
use App\Domain\Api\Services\Interfaces\ProductServiceInterface;
use App\Domain\Api\Validators\ProductValidator;
use App\Domain\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        ])->get();
    }

    /**
     * Get products with discount
     *
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function getProductsWithDiscount(): mixed
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
            $product = parent::store($request);

            $this->prices->create([
                'id' => (string)Str::orderedUuid(),
                'product_id' => $product->id,
                'original' => $request['original'],
                'final' => $request['final'],
                'discount_percentage' => $request['discount_percentage'],
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

            $product = parent::update($request, $id);

            $this->prices->update([
                'original' => $request['original'],
                'final' => $request['final'],
                'discount_percentage' => ($request['original'] - $request['final']) * 100 / $request['original'],
            ], $product->prices->first()->id);

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
