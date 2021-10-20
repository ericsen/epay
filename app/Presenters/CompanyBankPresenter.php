<?php

namespace App\Presenters;

use App\Transformers\CompanyBankTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class CompanyBankPresenter.
 *
 * @package namespace App\Presenters;
 */
class CompanyBankPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new CompanyBankTransformer();
    }
}
