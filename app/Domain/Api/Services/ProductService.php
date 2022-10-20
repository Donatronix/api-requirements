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
     * @param ProductValidator  $validator
     * @param PriceRepository   $priceRepository
     */
    public function __construct(
        protected ProductRepository $repository,
        protected ProductValidator  $validator,
        protected PriceRepository   $priceRepository
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
     * @param string $category
     *
     * @return mixed
     */
    public function getProductsByCategory(string $category): mixed
    {
        return $this->repository
            ->findWhere(['category', 'like', '%' . $category . '%'])
            ->get();
    }


    /**
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function getProductsWithDiscount(): mixed
    {
        return $this->repository->scopeQuery(function ($query) {
            return $query->whereHas('prices', function ($q) {
                return $q->whereNull('discount_percentage');
            });
        })->get();
    }

    public function store(array $request): mixed
    {
        try {
            DB::beginTransaction();
            $product = parent::store($request);
            $this->priceRepository->create(array_merge($request, ['product_id' => $product->id]));

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

            $product = parent::update($request,$id);
            $this->priceRepository->update($request, $product->price->id);

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

            $priceId=$this->repository->find($id)->price->id;
            $this->priceRepository->delete($priceId);
            $product = parent::delete($id);

            DB::commit();

            return $product;
        } catch (Throwable $e) {
            DB::rollBack();
            throw new RuntimeException($e->getMessage());
        }

    }
}
