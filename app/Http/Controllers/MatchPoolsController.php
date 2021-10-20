<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\MatchPoolCreateRequest;
use App\Http\Requests\MatchPoolUpdateRequest;
use App\Repositories\MatchPoolRepository;
use App\Validators\MatchPoolValidator;

/**
 * Class MatchPoolsController.
 * 
 * P.S. 預設的show及edit函示並沒有被使用, 故刪除
 *
 * @package namespace App\Http\Controllers;
 */
class MatchPoolsController extends Controller
{
    /**
     * @var MatchPoolRepository
     */
    protected $repository;

    /**
     * @var MatchPoolValidator
     */
    protected $validator;

    /**
     * MatchPoolsController constructor.
     *
     * @param MatchPoolRepository $repository
     * @param MatchPoolValidator $validator
     */
    public function __construct(MatchPoolRepository $repository, MatchPoolValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     * 撮合池管理頁面的控制函式
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $search = '';

        if (!empty(request()->search)) {
            $search = request()->search;
        }

        $page = $this->repository
            ->getMatchPoolsWithSearch($search)
            ->with('usersInTraderPool')
            ->orderBy('created_at', 'desc')
            ->paginate();
        
        $matchPools = collect($page->all());

        if (request()->wantsJson()) {
            return response()->json([
                'data' => $matchPools,
            ]);
        }

        return view('admin.match_pools.read', [
            'page'       => $page,
            'matchPools' => $matchPools,
            'search' => $search
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * 撮合池新增的控制函式
     *
     * @param  MatchPoolCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(MatchPoolCreateRequest $request)
    {
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
            $matchPoolSource = $request->all();
            //撮合池名稱(pool_name)是由撮合池代號(pool_display_name)經由md5雜湊產生
            //並非由前端輸入
            $matchPoolSource['pool_name'] = md5($matchPoolSource['pool_display_name']);

            $matchPool = $this->repository->create($matchPoolSource);

            $response = [
                'message' => __('contents.general.updated'),
                'data'    => $matchPool->toArray(),
            ];

            if ($request->wantsJson()) {
                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     * 撮合池編輯的控制函式
     *
     * @param  MatchPoolUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(MatchPoolUpdateRequest $request, $id)
    {
        try {
            $this->validator->setId($id);

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $matchPool = $this->repository->update($request->all(), $id);

            $response = [
                'message' => __('contents.general.updated'),
                'data'    => $matchPool->toArray(),
            ];

            if ($request->wantsJson()) {
                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     * 撮合池刪除的控制函式
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
                'message' => __('contents.general.deleted'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', __('contents.general.deleted'));
    }

    /**
     * Display a listing of which matchpool and trader related.
     * 撮合池註冊的控制函示
     * 並將所有matchPool的register_amount(已註冊人數)加上
     *
     * @return \Illuminate\Http\Response
     */
    public function traderInMatchPoolShow()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $search = '';

        if (!empty(request()->search)) {
            $search = request()->search;
        }

        $page = $this->repository
            ->getMatchPoolsWithSearch($search)
            ->with('usersInTraderPool')
            ->orderBy('created_at', 'desc')
            ->paginate();
        
        $matchPools = collect($page->all());

        if (request()->wantsJson()) {
            return response()->json([
                'data' => $matchPools,
            ]);
        }

        return view('admin.match_pools.matchPool_trader_read', [
            'page'       => $page,
            'matchPools' => $matchPools,
            'search'     => $search
        ]);
    }

    /**
     * Show the form for editing the App\Entities\TraderPool.
     * 撮合池註冊頁面編輯顯示的控制函式
     *
     * @param Integer $poolId App\Entities\MatchPool的id
     * @return \Illuminate\Http\Response
     *         - Object,  特定MatchPool
     *         - String,  特定MatchPool的代號
     *         - String,  特定MatchPool的描述
     *         - Integer, 特定MatchPool的id
     *         - Array,   特定MatchPool上，已註冊trader的id集合
     *         - Array,   所有符合條件(未註冊於其他pool)的traders的集合         
     */
    public function traderInMatchPoolEdit($poolId)
    {
        $matchPool            = $this->repository->find($poolId);
        $matchPoolDisplayName = $matchPool->pool_display_name;
        $matchPoolDescription = $matchPool->note;
        $matchPoolId          = $matchPool->id;
        $hasTraderIds         = $this->repository->checkUserIdsForMatchPool($poolId)->get()->pluck('user.id');
        $availableTraders     = $this->repository->getAvailableTraders($poolId)->get();

        return view(
            'admin.match_pools.matchPool_trader_edit',
            compact('matchPoolId', 'matchPoolDescription', 'matchPoolDisplayName', 'availableTraders', 'hasTraderIds')
        );
    }

    /**
     * Update the App\Entities\TraderPool in storage.
     * 撮合池註冊頁面編輯運作的控制函式
     *
     * @param Request $request 帶編輯內容的Illuminate\Http\Request
     * @param Integer $poolId  特定撮合池的id
     * @return \Illuminate\Http\Response
     */
    public function traderInMatchPoolUpdate(Request $request, $poolId)
    {
        $hasTraderIds = $this->repository->checkUserIdsForMatchPool($poolId)->get()->pluck('user.id');

        //刪除已註冊但不符合的關聯
        $sourceChanges = array_diff($hasTraderIds->toArray(), $request->all());
        if (!empty($sourceChanges)) {
            $this->repository->removeTraderMatchPool($poolId, $sourceChanges);
        }
        //添加新增且不重複的關聯
        $newChanges = array_diff($request->all(), $hasTraderIds->toArray());
        if (!empty($newChanges)) {
            $this->repository->updateTraderMatchPool($poolId, $newChanges);
        }
        
        $response = [
            'message' => __('contents.general.updated'),
        ];

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        return redirect()->back()->with('message', $response['message']);
    }
}
