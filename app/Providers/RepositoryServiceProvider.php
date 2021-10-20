<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionRepository::class, \App\Repositories\PermissionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RoleRepository::class, \App\Repositories\RoleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CheckUserRepository::class, \App\Repositories\CheckUserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TraderRepository::class, \App\Repositories\TraderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AgentRepository::class, \App\Repositories\AgentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CustomerRepository::class, \App\Repositories\CustomerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TradePaymentOrderRepository::class, \App\Repositories\TradePaymentOrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\MatchPoolRepository::class, \App\Repositories\MatchPoolRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TraderPoolRepository::class, \App\Repositories\TraderPoolRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CompanyBankRepository::class, \App\Repositories\CompanyBankRepositoryEloquent::class);
        //:end-bindings:
    }
}
