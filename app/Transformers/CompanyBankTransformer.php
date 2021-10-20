<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\CompanyBank;

/**
 * Class CompanyBankTransformer.
 *
 * @package namespace App\Transformers;
 */
class CompanyBankTransformer extends TransformerAbstract
{
    /**
     * Transform the CompanyBank entity.
     *
     * @param \App\Entities\CompanyBank $model
     *
     * @return array
     */
    public function transform(CompanyBank $model)
    {
        return [
            'id'         => (int) $model->id,
            'enable'     => $model->enable == 'on' ? true : false,
            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
