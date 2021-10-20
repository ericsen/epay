<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\CheckUser;

/**
 * Class CheckUserTransformer.
 *
 * @package namespace App\Transformers;
 */
class CheckUserTransformer extends TransformerAbstract
{
    /**
     * Transform the CheckUser entity.
     *
     * @param \App\Entities\CheckUser $model
     *
     * @return array
     */
    public function transform(CheckUser $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
