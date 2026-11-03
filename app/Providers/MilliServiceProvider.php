<?php

namespace App\Providers;

use App\Console\Commands\SetupIndustryCommand;
use App\Http\Middleware\PrefixAbsolutePaths;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Milli kit bootstrapping.
 *
 * Shipped separately so starter-kit installs do not overwrite the site's
 * AppServiceProvider. Register in bootstrap/providers.php — see README.
 *
 * Career uploads are validated by the form blueprint and resumes asset
 * container (mimes + max).
 */
class MilliServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([SetupIndustryCommand::class]);
        }

        // Private resume uploads — local disk with no public URL.
        // Files land in storage/app/resumes (outside public/), not public/assets.
        config([
            'filesystems.disks.resumes' => [
                'driver' => 'local',
                'root' => storage_path('app/resumes'),
                'visibility' => 'private',
                'throw' => false,
                'report' => false,
            ],
        ]);

        $extra = config('statamic.assets.additional_uploadable_extensions', []);
        config([
            'statamic.assets.additional_uploadable_extensions' => array_values(array_unique(array_merge(
                is_array($extra) ? $extra : [],
                ['doc', 'docx']
            ))),
        ]);

        // Prefer Milli content indexes when the host site still has the default "all/title" search config.
        $indexes = config('statamic.search.indexes', []);
        if (isset($indexes['default']) && ($indexes['default']['searchables'] ?? null) === 'all') {
            $indexes['default']['searchables'] = [
                'collection:news',
                'collection:culture',
                'collection:editions',
                'collection:workshops',
                'collection:pages',
                'collection:careers',
            ];
            $indexes['default']['fields'] = ['title', 'text', 'heading', 'excerpt'];
            config(['statamic.search.indexes' => $indexes]);
        }

        $this->app->make('router')->pushMiddlewareToGroup('web', PrefixAbsolutePaths::class);

        $this->app->booted(function () {
            if (! collect(Route::getRoutes())->contains(fn ($route) => $route->uri() === 'search')) {
                Route::statamic('/search', 'search', ['title' => 'Search']);
            }
        });
    }
}
