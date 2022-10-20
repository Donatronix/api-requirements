<?php

namespace App\Domain\Api\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Domain\Api\Repositories\Contracts\ProductRepository;
use App\Domain\Api\Models\Product;
use App\Domain\Api\Validators\ProductValidator;

/**
 * Class ProductRepositoryEloquent.
 *
 * @package namespace App\Domain\Api\Repositories;
 */
class ProductRepositoryEloquent extends BaseRepository implements ProductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Product::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ProductValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
