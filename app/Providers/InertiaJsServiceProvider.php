<?php

namespace App\Providers;

use App\Http\Resources\AuthenticatedUserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use function __;
use function base_path;
use function file_get_contents;
use function json_decode;

class InertiaJsServiceProvider extends ServiceProvider
{
    public function register()
    {
        Inertia::version(function () {
            return md5_file(public_path('mix-manifest.json'));
        });

        Inertia::share([
            'translations' => function () {
                return json_decode(file_get_contents(base_path('resources/lang/'.Lang::getLocale().'.json')));
            },
            'auth' => function () {
                return Auth::check() ? new AuthenticatedUserResource(Auth::user()) : null;
            },
            'flash' => function () {
                return [
                    'success' => Session::get('success'),
                    'info' => Session::get('info'),
                ];
            },
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
        ]);
    }
}
