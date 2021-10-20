<?php

namespace App\Presenters;

use App\Transformers\TraderTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class TraderPresenter.
 *
 * @package namespace App\Presenters;
 */
class TraderPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new TraderTransformer();
    }
}
