<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto create storage symlink if not exists, and handle broken symlinks
        $storageLinkPath = public_path('storage');
        if (is_link($storageLinkPath) && !file_exists($storageLinkPath)) {
            @unlink($storageLinkPath);
        }
        if (!file_exists($storageLinkPath)) {
            app()->make('files')->link(
                storage_path('app/public'),
                $storageLinkPath
            );
        }

        \Illuminate\Support\Facades\Gate::define('viewApiDocs', function ($user = null) {
            return true;
        });

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'Bearer Token')
            );
        });
    }
}
