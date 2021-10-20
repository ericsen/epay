<?php

namespace App\Transformers;

use App\Entities\TradePaymentOrder;
use League\Fractal\TransformerAbstract;

/**
 * Class TradePaymentOrderTransformer.
 *
 * @package namespace App\Transformers;
 */
class TradePaymentOrderTransformer extends TransformerAbstract
{
    /**
     * Transform the TradePaymentOrder entity.
     *
     * @param \App\Entities\TradePaymentOrder $model
     *
     * @return array
     */
    public function transform(TradePaymentOrder $model)
    {
        $transform = collect($model->toArray());
        $status    = $model->status;
        switch ($status) {
            case 'success':
                $status = '交易成功';
                break;
            case 'pending':
                $status = '等待審核中...';
                break;
            case 'fail':
                $status = '交易失敗';
                break;
        }
        return $transform->merge([
            'id'         => (int) $model->id,

            /* place your other model properties here */
            'status'     => $status,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ])->toArray();
    }
}
