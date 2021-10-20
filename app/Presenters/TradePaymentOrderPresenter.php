<?php

namespace App\Presenters;

use App\Transformers\TradePaymentOrderTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class TradePaymentOrderPresenter.
 *
 * @package namespace App\Presenters;
 */
class TradePaymentOrderPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new TradePaymentOrderTransformer();
    }
}
