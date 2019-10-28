<?php

namespace App\Providers;

use App\Filesystem\FileIndex;
use App\User;
use Laravel\Nova\Nova;
use Laravel\Nova\Cards\Help;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Resource;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Vyuldashev\NovaPermission\NovaPermissionTool;
use function app_path;
use function array_unique;
use function collect;
use function is_dir;
use function iterator_to_array;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->domainResources();
    }

    /**
     * Register the Nova resources in domain directories.
     */
    public function domainResources()
    {
        collect(
            (new Finder)->directories()->depth(1)->in(app_path('Domains'))
        )->map(function (SplFileInfo $fileInfo) {
            return $fileInfo->getRelativePath();
        })
        ->unique()
        ->map(function (string $relativePath) {
            if (is_dir($domainNovaPath = app_path("Domains/{$relativePath}/Nova"))) {
                return $domainNovaPath;
            }

            return null;
        })
        ->filter()
        ->each(function (string $novaDirectory) {
            Nova::resourcesIn($novaDirectory);
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function () {
            return true;
        });

//        Gate::define('viewNova', function (?User $user) {
//            return $user && $user->hasPermissionTo('view Nova');
//        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new Help,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            NovaPermissionTool::make()
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
