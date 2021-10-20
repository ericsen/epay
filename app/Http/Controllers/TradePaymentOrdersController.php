<?php

namespace App\Http\Controllers;

use App\Entities\TradePaymentOrder;
use App\Entities\User;
use App\Events\PushPaymentStatusToTrader;
use App\Events\PushTraderPaymentOrderToBackEnd;
use App\Http\Requests\TradePaymentOrderCreateRequest;
use App\Http\Requests\TradePaymentOrderUpdateRequest;
use App\Repositories\TradePaymentOrderRepository;
use App\Validators\TradePaymentOrderValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class TradePaymentOrdersController.
 *
 * @package namespace App\Http\Controllers;
 */
class TradePaymentOrdersController extends Controller
{
    /**
     * @var TradePaymentOrderRepository
     */
    protected $repository;

    /**
     * @var TradePaymentOrderValidator
     */
    protected $validator;

    /**
     * TradePaymentOrdersController constructor.
     *
     * @param TradePaymentOrderRepository $repository
     * @param TradePaymentOrderValidator $validator
     */
    public function __construct(TradePaymentOrderRepository $repository, TradePaymentOrderValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * uniqueId
     * 生成16位以上唯一ID
     *
     * @param int $length 不含前綴的長度，最小16，建議20+
     * @param str $prefix 前缀
     * @return str $id
     */
    private function uniqueId($length = 16, $prefix = '')
    {
        $id        = $prefix;
        $addLength = $length - 13;
        $id .= uniqid();
        if (function_exists('random_bytes')) {
            $id .= substr(bin2hex(random_bytes(ceil(($addLength) / 2))), 0, $addLength);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $id .= substr(bin2hex(openssl_random_pseudo_bytes(ceil($addLength / 2))), 0, $addLength);
        } else {
            $id .= mt_rand(1 * pow(10, ($addLength)), 9 * pow(10, ($addLength)));
        }
        return $id;
    }

    /**
     * 產生儲值單編號
     * app.name + 20碼不重複隨機數 = 共24碼
     *
     * @return void
     */
    private function uniqueSnGenerator()
    {
        return $this->uniqueId(20, strtolower(config('app.name')));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $search = '';
        $select = '';

        if (!empty(request()->search)) {
            $search = request()->search;
        }
        if (!empty(request()->select)) {
            $select = request()->select;
        }

        $page = $this->repository
            ->getOrderInfoWithSearch($search, $select)
            ->orderByRaw('-trade_payment_orders.checked_at asc')
            ->orderBy('trade_payment_orders.created_at', 'desc')
            ->paginate();

        $orders = collect($page->all());
        // debug($orders->toArray());
        if (request()->wantsJson()) {

            return response()->json([
                'data' => $orders,
            ]);
        }

        return view('admin.traders.payment.read', [
            'page'   => $page,
            'orders' => $orders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TradePaymentOrderCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(TradePaymentOrderCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->setAttributes(__('validation.attributes.trader_payment'))->passesOrFail(ValidatorInterface::RULE_CREATE);

            $requestAll                     = $request->all();
            $requestAll['payment_order_sn'] = $this->uniqueSnGenerator();

            $trader = User::with('trader')->find($requestAll['user_id']);
            // dd($trader);
            $requestAll['user_name']     = $trader->name;
            $requestAll['user_nickname'] = $trader->nickname;
            $requestAll['trader_id']     = $trader->trader->id;

            $tradePaymentOrder = $this->repository->create($requestAll);

            /**
             * 通知後台帳號，有交易員儲值，等待驗證
             */
            event(new PushTraderPaymentOrderToBackEnd(__('contents.traders.payment.push_trader_payment_order_to_backend', ['name' => $requestAll['user_name'], 'amount' => $requestAll['amount']]), $trader));

            $response = [
                'message' => __('contents.traders.payment.trader_payment_created'),
                'data'    => $tradePaymentOrder->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $tradePaymentOrder = $this->repository->with('user')->with('trader')->with('inspector')->setPresenter("App\Presenters\TradePaymentOrderPresenter")->find($id);
        $tradePaymentOrder = $this->repository->with('user')->with('trader')->with('inspector')->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $tradePaymentOrder,
            ]);
        }

        return view('admin.traders.payment.show', [
            'order' => $tradePaymentOrder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tradePaymentOrder = $this->repository->with('user')->with('trader')->find($id);

        return view('admin.traders.payment.edit', [
            'order' => $tradePaymentOrder,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TradePaymentOrderUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(TradePaymentOrderUpdateRequest $request, $id)
    {

        try {
            // dd($request->all());
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $requestAll                 = $request->all();
            $requestAll['inspector_id'] = Auth::user()->id;
            $requestAll['checked_at']   = now();

            $paymentOrderSn = $requestAll['payment_order_sn'];
            $amount         = $requestAll['amount'];
            $note           = $requestAll['note'];

            unset($requestAll['user_name']);
            unset($requestAll['user_nickname']);
            unset($requestAll['payment_order_sn']);
            unset($requestAll['amount']);

            try {

                //高併發處理
                DB::beginTransaction();
                $tradePaymentOrder = TradePaymentOrder::where('status', '=', 'pending')->lockForUpdate()->findOrFail($id);
                $tradePaymentOrder->update($requestAll);
                // sleep(10);
                DB::commit();

            } catch (ModelNotFoundException $e) {

                if ($request->wantsJson()) {

                    return response()->json([
                        'error'   => true,
                        'message' => $e->getMessageBag(),
                    ]);
                }

                return redirect()->back()->withErrors($e->getMessageBag())->withInput();
            }

            $trader = User::with('trader')->find($requestAll['user_id']);

            //儲值成功，撥點 + 通知
            if ($requestAll['status'] === 'success') {
                $userRepository = app('App\Repositories\UserRepository');
                $userRepository->addPaymentAmountToTrader($requestAll['user_id'], $amount);
                /**
                 * @todo 推播通知
                 */
                event(new PushPaymentStatusToTrader(
                    __('contents.traders.payment.push_trader_payment_success_order_to_trader', [
                        'payment_order_sn' => $paymentOrderSn,
                        'amount'           => $amount,
                    ]), $trader, $tradePaymentOrder)
                );
            }

            //儲值失敗，通知
            if ($requestAll['status'] === 'fail') {
                /**
                 * @todo 推播通知
                 */
                event(new PushPaymentStatusToTrader(
                    __('contents.traders.payment.push_trader_payment_fail_order_to_trader', [
                        'payment_order_sn' => $paymentOrderSn,
                        'amount'           => $amount,
                        'note'             => $note,
                    ]), $trader, $tradePaymentOrder)
                );
            }

            $response = [
                'message' => __('contents.traders.payment.trader_payment_updated'),
                'data'    => $tradePaymentOrder->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'TradePaymentOrder deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'TradePaymentOrder deleted.');
    }

    public function create()
    {
        return 'Jovi test...';
    }
}
