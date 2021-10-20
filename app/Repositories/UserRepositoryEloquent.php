<?php

namespace App\Repositories;

use App\Entities\User;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{

    /**
     * 定義可以搜尋的條件
     *
     * @var array
     */
    protected $fieldSearchable = [
        'id'    => '=',
        'name'  => 'like',
        'email' => 'like',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {
        return UserValidator::class;
    }

    // public function presenter()
    // {
    //     return UserPresenter::class;
    //     // return "App\\Presenters\\UserPresenter";
    // }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getUserDetailInfo()
    {
        if (Auth::user()->hasRole('super')) {
            return $this->with('roles.permissions')
                ->with('permissions')
                ->with('inspector')
                ->with('parent')
                ->with('children');
        } else {
            return $this->with(['roles' => function ($query) {
                $query->where('name', '!=', 'super')
                    ->with(['permissions' => function ($query) {
                        $query->where('name', 'NOT LIKE', 'permissions.*');
                    }]);
            }])->with(['permissions' => function ($query) {
                $query->where('name', 'NOT LIKE', 'permissions.*');
            }])->with('inspector')
                ->with('parent')
                ->with('children');
        }
    }

    /**
     * 取得帳號基本資料
     *
     * @return void
     */
    public function getUserInfoWithSearch($search = '')
    {
        if (Auth::user()->hasRole('super')) {
            if (!empty($search)) {
                return $this->model
                    ->with('roles.permissions')
                    ->with('permissions')
                    ->with('inspector')
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nickname', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('note', 'like', '%' . $search . '%');
                    });
            } else {
                return $this->model
                    ->with('roles.permissions')
                    ->with('permissions')
                    ->with('inspector');
            }
        } else {
            if (!empty($search)) {
                return $this->model->whereHas('roles', function ($query) {
                    $query->where('name', '!=', 'super');
                })->with(['roles' => function ($query) {
                    $query->where('name', '!=', 'super')
                        ->with(['permissions' => function ($query) {
                            $query->where('name', 'NOT LIKE', 'permissions.*');
                        }]);
                }])->with(['permissions' => function ($query) {
                    $query->where('name', 'NOT LIKE', 'permissions.*');
                }])->with('inspector')
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nickname', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('note', 'like', '%' . $search . '%');
                    });
            } else {
                return $this->model->whereHas('roles', function ($query) {
                    $query->where('name', '!=', 'super');
                })->with(['roles' => function ($query) {
                    $query->where('name', '!=', 'super')
                        ->with(['permissions' => function ($query) {
                            $query->where('name', 'NOT LIKE', 'permissions.*');
                        }]);
                }])->with(['permissions' => function ($query) {
                    $query->where('name', 'NOT LIKE', 'permissions.*');
                }])->with('inspector');
            }
        }
    }

    /**
     * 取得特定帳號基本資料 - For Edit
     *
     * @return void
     */
    public function getUserForEdit()
    {
        if (Auth::user()->hasRole('super')) {
            return $this->with(['roles' => function ($query) {
                $query->select('id', 'name', 'display_name', 'description')
                    ->with(['permissions' => function ($query) {
                        $query->select('id', 'name', 'display_name');
                    }]);
            }])->with(['permissions' => function ($query) {
                $query->select('id', 'name', 'display_name');
            }])->with('parent')->with('children')->with('checks.inspector');
        } else {
            return $this->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'super');
            })->with(['roles' => function ($query) {
                $query->where('name', '!=', 'super')->select('id', 'name', 'display_name', 'description')
                    ->with(['permissions' => function ($query) {
                        $query->where('name', 'NOT LIKE', 'permissions.*')->select('id', 'name', 'display_name');
                    }]);
            }])->with(['permissions' => function ($query) {
                $query->where('name', 'NOT LIKE', 'permissions.*')->select('id', 'name', 'display_name');
            }])->with('parent')->with('children')->with('checks.inspector');
        }
    }

    public function syncExtraData($data, $user)
    {
        switch ($user->identity) {
            case 'B':
                return null;
                break;
            case 'T':
                $traderRepository = app('App\Repositories\TraderRepository');
                return $traderRepository->updateOrCreate(['user_id' => $user->id], $data);
                break;
            case 'A':
                $agentRepository = app('App\Repositories\AgentRepository');
                return $agentRepository->updateOrCreate(['user_id' => $user->id], $data);
                break;
            case 'C':
                $CustomerRepository = app('App\Repositories\CustomerRepository');
                return $CustomerRepository->updateOrCreate(['user_id' => $user->id], $data);
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * 查詢各帳號身份允許的代理商
     * 排除自已
     *
     * @return void
     */
    public function getParentsByIdentity($parentIdentity = '', $user_id = '')
    {
        $query = $this->getUserInfoWithSearch()
            ->where('identity', '=', $parentIdentity)
            ->where('enable', '=', 'on')
            ->orderBy('name', 'asc');

        if (!empty($user_id)) {
            return $query->where('id', '!=', $user_id);
        } else {
            return $query;
        }
    }

    /**
     * 取得後端帳號
     *
     * @return void
     */
    public function getAdminsWithSearch($search = '')
    {
        // debug($this->getUserInfoWithSearch($search)->where('identity', '=', 'B'));
        return $this->getUserInfoWithSearch($search)->where('identity', '=', 'B');
    }

    /**
     * 取得交易員帳號
     *
     * @return void
     */
    public function getTradersWithSearch($search = '')
    {
        return $this->getUserInfoWithSearch($search)->where('identity', '=', 'T');
    }

    /**
     * 取得代理商帳號
     *
     * @return void
     */
    public function getAgentsWithSearch($search = '')
    {
        return $this->getUserInfoWithSearch($search)->where('identity', '=', 'A');
    }

    /**
     * 取得商戶帳號
     *
     * @return void
     */
    public function getCustomersWithSearch($search = '')
    {
        return $this->getUserInfoWithSearch($search)->where('identity', '=', 'C');
    }

    /**
     * 交易員儲值
     *
     * @param [type] $user_id
     * @param integer $amount
     * @return void
     */
    public function addPaymentAmountToTrader($user_id, $amount = 0)
    {
        $trader = $this->model->lockForUpdate()->find($user_id);
        $trader->total_deposit += $amount;
        $trader->save();
        return $trader;
    }

}
