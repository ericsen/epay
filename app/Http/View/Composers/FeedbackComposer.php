<?php

namespace App\Http\View\Composers;

use Auth;
use Illuminate\View\View;

class FeedbackComposer
{

    protected $user;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository $users
     * @return void
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {

        /**
         * @todo 等交易員回報功能完成後接續撰寫
         */

        $feedbacks = [
            [
                'subject'     => 'test 1',
                'description' => 'description 1',
                'url'         => '/accounts/traders/show/7',
            ],
            [
                'subject'     => 'test 2',
                'description' => 'description 2',
                'url'         => '/accounts/traders/show/6',
            ],
        ];

        $data = [
            'feedbacks' => collect($feedbacks),
            'count'     => 2,
        ];

        $view->with('data', $data);
    }
}
