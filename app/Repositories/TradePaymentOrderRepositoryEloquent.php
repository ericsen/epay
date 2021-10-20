<?php

namespace App\Repositories;

use App\Entities\TradePaymentOrder;
use App\Repositories\TradePaymentOrderRepository;
use App\Validators\TradePaymentOrderValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class TradePaymentOrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TradePaymentOrderRepositoryEloquent extends BaseRepository implements TradePaymentOrderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TradePaymentOrder::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return TradePaymentOrderValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getOrderInfoWithSearch($search = '', $select = '')
    {
        if (!empty($search)) {
            $sql = $this->model
                ->select([
                    "trade_payment_orders.*",
                    "users.name",
                    "users.email",
                    "users.nickname",
                ])
                ->leftJoin('users', 'users.id', '=', 'trade_payment_orders.user_id')
                ->where(function ($query) use ($search) {
                    $query->where('trade_payment_orders.payment_order_sn', 'like', '%' . $search . '%')
                        ->orWhere('trade_payment_orders.bank_name', 'like', '%' . $search . '%')
                        ->orWhere('trade_payment_orders.bank_branch', 'like', '%' . $search . '%')
                        ->orWhere('trade_payment_orders.bank_account_name', 'like', '%' . $search . '%')
                        ->orWhere('trade_payment_orders.bank_account_number', 'like', '%' . $search . '%')
                        ->orWhere('trade_payment_orders.bank_order_sn', 'like', '%' . $search . '%')
                        ->orWhere('trade_payment_orders.user_name', 'like', '%' . $search . '%')
                        ->orWhere('trade_payment_orders.user_nickname', 'like', '%' . $search . '%')
                        ->orWhere('users.name', 'like', '%' . $search . '%')
                        ->orWhere('users.email', 'like', '%' . $search . '%')
                        ->orWhere('users.nickname', 'like', '%' . $search . '%');
                })->with('inspector');
        } else {
            $sql = $this->model
                ->select([
                    "trade_payment_orders.*",
                    "users.name",
                    "users.email",
                    "users.nickname",
                ])
                ->leftJoin('users', 'users.id', '=', 'trade_payment_orders.user_id')
                ->with('inspector');
        }

        if (!empty($select)) {
            $sql->where('trade_payment_orders.status', '=', $select);
        }

        return $sql;

    }




}
