<?php

namespace App\Repositories;

use App\Entities\User;
use App\Entities\MatchPool;
use App\Entities\TraderPool;
use App\Validators\MatchPoolValidator;
use App\Repositories\MatchPoolRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class MatchPoolRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MatchPoolRepositoryEloquent extends BaseRepository implements MatchPoolRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MatchPool::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {
        return MatchPoolValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * 依關鍵字取出App\Entities\MatchPool
     *
     * @param string $search 搜尋的關鍵字, 預設為空
     * @return Eloquent
     */
    public function getMatchPoolsWithSearch($search='')
    {
        return (empty($search)) ?
                    $this->model :
                    $this->model
                    ->where(function ($query) use ($search) {
                        $query->where('pool_display_name', 'like', '%' . $search . '%')
                        ->orWhere('note', 'like', '%' . $search . '%');
                    });
    }

    /**
     * 取得可供選擇的App\Entities\Trader
     * - 尚未註冊的trader
     * - 註冊於該poolId的trader
     *
     * @param integer $poolId 撮合池id
     * @return Eloquent
     */
    public function getAvailableTraders($poolId)
    {
        $userIdNoMatch = $this->checkUserIdsForMatchPool($poolId, false)
                ->get()->pluck('user_id')->toArray();

        //filter掉不合條件的部份, 取出剩下適合的部份
        $userMatch = User::where('identity', '=', 'T')
                ->where('enable', '=', 'on')
                ->whereNotIn('id', $userIdNoMatch);

        return $userMatch;
    }

    /**
     * 依條件取出App\Entites\User的id集合
     *
     * @param integer $poolId    撮合池id
     * @param boolean $matchable 符合條件,預設為true
     *                           - true :符合
     *                           - false:不符合
     * @return Eloquent
     */
    public function checkUserIdsForMatchPool($poolId, $matchable=true)
    {
        return TraderPool::where('match_pool_id', (($matchable) ? '=' : '!='), $poolId)->with(['user' => function ($query) {
            $query->select('id');
        }]);
    }

    /**
     * 依對應條件寫入trader_pool
     *
     * @param integer $poolId    撮合池id
     * @param array   $traderIds 交易員id的集合
     * @return void
     */
    public function updateTraderMatchPool($poolId, $traderIds)
    {
        $dataSet = [];
        foreach ($traderIds as $traderId) {
            $dataSet[] = [
                'match_pool_id' => $poolId,
                'user_id' => $traderId
            ];
        }
        TraderPool::insert($dataSet);
    }

    /**
     * 依對應條件刪除trader_pool內的資料
     *
     * @param integer $poolId    撮合池id
     * @param array   $traderIds 交易員id的集合
     * @return void
     */
    public function removeTraderMatchPool($poolId, $traderIds)
    {
        TraderPool::where('match_pool_id', '=', $poolId)
                ->whereIn('user_id', $traderIds)->delete();
    }
}
