<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CheckUserRepository;
use App\Entities\CheckUser;
use App\Validators\CheckUserValidator;

/**
 * Class CheckUserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CheckUserRepositoryEloquent extends BaseRepository implements CheckUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CheckUser::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return CheckUserValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    /**
     * 取得驗證資訊
     *
     * @param [type] $id
     * @return void
     */
    public function getCheckById($id){
        return $this->model->where('id', '=', $id);
    }



}
