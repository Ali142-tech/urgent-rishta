<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
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

        // Throttles both App\Jobs\SendCampaignEmailJob and
        // App\Jobs\SendProfileCompletionEmailJob (shared, so their combined
        // throughput — not each job type separately — stays under the
        // account's real SES limit) — without this registered, the
        // RateLimited('campaign-mail') middleware throws "Rate limiter
        // [campaign-mail] is not defined" on every attempt.
        //
        // Set to the account's actual approved SES max send rate (14/sec,
        // per the AWS production-access approval email) rather than an
        // arbitrary conservative number — SES itself will reject anything
        // faster with a Throttling error, so there's no point staying under it.
        RateLimiter::for('campaign-mail', function () {
            return Limit::perSecond((int) env('SES_MAX_SEND_RATE', 14));
        });
    }
}
