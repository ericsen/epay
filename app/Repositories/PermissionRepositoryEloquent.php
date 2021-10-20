<?php

namespace App\Repositories;

use App\Entities\Permission;
use Illuminate\Support\Facades\Auth;
use App\Validators\PermissionValidator;
use App\Repositories\PermissionRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PermissionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PermissionRepositoryEloquent extends BaseRepository implements PermissionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permission::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {
        return PermissionValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 取得系統上所有的權限
     *
     * @return void
     */
    public function getAllPermissionsForEdit()
    {
        if (Auth::user()->hasRole('super')) {
            return $this->model->get(['id', 'name', 'display_name', 'use_in', 'description']);
        } else {
            return $this->model->where('name', 'NOT LIKE', 'permissions.%')->get(['id', 'name', 'display_name', 'use_in', 'description']);
        }

    }

}
