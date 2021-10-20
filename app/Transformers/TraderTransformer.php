<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Trader;

/**
 * Class TraderTransformer.
 *
 * @package namespace App\Transformers;
 */
class TraderTransformer extends TransformerAbstract
{
    /**
     * Transform the Trader entity.
     *
     * @param \App\Entities\Trader $model
     *
     * @return array
     */
    public function transform(Trader $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
