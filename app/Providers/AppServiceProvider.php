<?php

namespace App\Providers;

use App\Models\Rekam;
use App\Observers\ItemObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Post;
use App\Observers\PostObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        date_default_timezone_set('Asia/Singapore');
        // Post::observe(PostObserver::class);
        
        // Log database queries to a dedicated channel
        if (!$this->app->runningInConsole()) {
            
            // Set up DB listener separately
            
        }
    }
}
