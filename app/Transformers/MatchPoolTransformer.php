<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\MatchPool;

/**
 * Class MatchPoolTransformer.
 *
 * @package namespace App\Transformers;
 */
class MatchPoolTransformer extends TransformerAbstract
{
    /**
     * Transform the MatchPool entity.
     *
     * @param \App\Entities\MatchPool $model
     *
     * @return array
     */
    public function transform(MatchPool $model)
    {
        return [
            'id'         => (int) $model->id,
            'enable' => $model->enable == 'on' ? true : false,
            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
