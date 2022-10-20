<?php

namespace App\Domain\Api\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Domain\Api\Repositories\Contracts\PriceRepository;
use App\Domain\Api\Models\Price;
use App\Domain\Api\Validators\PriceValidator;

/**
 * Class PriceRepositoryEloquent.
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
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
