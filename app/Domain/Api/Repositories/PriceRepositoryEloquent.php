<?php

namespace App\Domain\Api\Repositories;

use App\Domain\Api\Models\Price;
use App\Domain\Api\Repositories\Contracts\PriceRepository;
use App\Domain\Api\Validators\PriceValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ProductRepositoryEloquent.
 *
 * @package namespace App\Domain\Api\Repositories;
 */
class PriceRepositoryEloquent extends BaseRepository implements PriceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Price::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return PriceValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
