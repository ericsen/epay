<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TraderPoolRepository;
use App\Entities\TraderPool;
use App\Validators\TraderPoolValidator;

/**
 * Class TraderPoolRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TraderPoolRepositoryEloquent extends BaseRepository implements TraderPoolRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TraderPool::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
