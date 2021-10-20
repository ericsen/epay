<?php

namespace App\Repositories;

use App\Entities\Role;
use App\Repositories\RoleRepository;
use App\Validators\RoleValidator;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class RoleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RoleRepositoryEloquent extends BaseRepository implements RoleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Role::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return RoleValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 取得所有角色身上擁有的權限 - 編輯帳號或權限時使用
     *
     * @param [type] $useFor
     * @return void
     */
    public function getAllRolePermissionsForEdit($useFor = ['B', 'A', 'T', 'C'])
    {
        if (Auth::user()->hasRole('super')) {
            return $this->model->with(['permissions' => function ($query) {
                $query->select('id', 'name', 'display_name');
            }])->whereIn('use_for', $useFor)->orderBy('name', 'desc')->get(['id', 'name', 'display_name', 'use_for', 'description']);
        } else {
            return $this->model->with(['permissions' => function ($query) {
                $query->select('id', 'name', 'display_name')->where('name', 'NOT LIKE', 'permissions.%');
            }])->where('name', '!=', 'super')->whereIn('use_for', $useFor)->orderBy('name', 'desc')->get(['id', 'name', 'display_name', 'use_for', 'description']);
        }
    }

    /**
     * 取得特定角色身上所有的權限id
     *
     * @param [type] $roleId
     * @return void
     */
    public function getHasPermissionIds($roleId)
    {
        if (Auth::user()->hasRole('super')) {
            return $this->model->with(['permissions' => function ($query) {
                $query->select('id');
            }])->find($roleId);
        } else {
            return $this->model->with(['permissions' => function ($query) {
                $query->select('id')->where('name', 'NOT LIKE', 'permissions.%');
            }])->find($roleId);
        }
    }

    /**
     * 取得特定角色身上所有的權限代號
     *
     * @param [type] $roleId
     * @return void
     */
    public function getHasPermissionNames($roleId)
    {
        if (Auth::user()->hasRole('super')) {
            return $this->model->with(['permissions' => function ($query) {
                $query->select('name');
            }])->find($roleId);
        } else {
            return $this->model->with(['permissions' => function ($query) {
                $query->select('name')->where('name', 'NOT LIKE', 'permissions.%');
            }])->find($roleId);
        }
    }

    /**
     * 取得特定角色身上所有的權限資料
     *
     * @param [type] $roleId
     * @return void
     */
    public function getHasPermissions($roleId)
    {
        if (Auth::user()->hasRole('super')) {
            return $this->model->with('permissions')->find($roleId);
        } else {
            return $this->model->with(['permissions' => function ($query) {
                $query->where('name', 'NOT LIKE', 'permissions.%');
            }])->find($roleId);
        }
    }

}
