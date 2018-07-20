<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\Backend\BranchPara\BranchParaInterface', 'App\Repositories\Backend\BranchPara\BranchRepository');
        $this->app->bind('App\Repositories\Backend\Report\ReportInterface', 'App\Repositories\Backend\Report\ReportRepository');
    }
}
