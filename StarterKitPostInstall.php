<?php

use Illuminate\Support\Facades\File;

/**
 * Registers MilliServiceProvider without touching AppServiceProvider.
 */
class StarterKitPostInstall
{
    public function handle($console): void
    {
        $providersPath = base_path('bootstrap/providers.php');
        $class = 'App\\Providers\\MilliServiceProvider::class';

        if (! File::exists(base_path('app/Providers/MilliServiceProvider.php'))) {
            $console->warn('MilliServiceProvider.php was not installed; skip provider registration.');

            return;
        }

        if (! File::exists($providersPath)) {
            $console->warn('bootstrap/providers.php not found. Register App\\Providers\\MilliServiceProvider manually (see README).');

            return;
        }

        $contents = File::get($providersPath);

        if (str_contains($contents, 'MilliServiceProvider')) {
            $console->info('MilliServiceProvider is already registered.');

            return;
        }

        if (! preg_match('/return\s*\[/', $contents)) {
            $console->warn('Could not update bootstrap/providers.php automatically. Add App\\Providers\\MilliServiceProvider::class (see README).');

            return;
        }

        $updated = preg_replace(
            '/return\s*\[/',
            "return [\n    {$class},",
            $contents,
            1
        );

        if ($updated === null || $updated === $contents) {
            $console->warn('Could not update bootstrap/providers.php automatically. Add App\\Providers\\MilliServiceProvider::class (see README).');

            return;
        }

        File::put($providersPath, $updated);
        $console->info('Registered App\\Providers\\MilliServiceProvider in bootstrap/providers.php.');
    }
}
