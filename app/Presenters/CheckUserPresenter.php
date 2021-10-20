<?php

namespace App\Presenters;

use App\Transformers\CheckUserTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class CheckUserPresenter.
 *
 * @package namespace App\Presenters;
 */
class CheckUserPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new CheckUserTransformer();
    }
}
