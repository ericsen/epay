<?php

namespace App\Presenters;

use App\Transformers\MatchPoolTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class MatchPoolPresenter.
 *
 * @package namespace App\Presenters;
 */
class MatchPoolPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new MatchPoolTransformer();
    }
}
