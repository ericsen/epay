<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        View::composer('admin.layouts.notifications.feedback', 'App\Http\View\Composers\FeedbackComposer');
        // View::composer('admin.layouts.notifications.feedback', 'App\Http\View\Composers\FeedbackComposer');
        // View::composer('admin.layouts.notifications.feedback', 'App\Http\View\Composers\FeedbackComposer');
        // View::composer('admin.layouts.notifications.feedback', 'App\Http\View\Composers\FeedbackComposer');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
