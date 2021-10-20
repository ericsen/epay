<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CompanyBankRepository;
use App\Entities\CompanyBank;
use App\Validators\CompanyBankValidator;

/**
 * Class CompanyBankRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CompanyBankRepositoryEloquent extends BaseRepository implements CompanyBankRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CompanyBank::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return CompanyBankValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
