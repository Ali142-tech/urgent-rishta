<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        // The app only loads Bootstrap 4 (see head-assets.blade.php), not
        // Tailwind — so Laravel's default ->links() view (Tailwind, styled
        // via utility classes like h-5/w-5) renders its prev/next SVG arrows
        // completely unstyled at huge native size. Use the Bootstrap 4
        // pagination view everywhere instead, which the site's CSS already
        // supports.
        Paginator::useBootstrapFour();
    }
}
