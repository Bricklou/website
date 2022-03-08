<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserSocialsLinks;
use Illuminate\Support\ServiceProvider;
use Orchid\Support\Facades\Dashboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dashboard $dashboard)
    {
        $links = [];

        $user = User::query()->whereHas('roles', function ($query) {
            $query->where('slug', '=', 'admin');
        })->first();

        if ($user) {
            foreach ($user->socialLinks as $social) {
                array_push($links, [
                    'network' => $social->social_network,
                    'link' => $social->social_link,
                    'name' => UserSocialsLinks::$SOCIAL_LINKS[$social->social_network],
                ]);
            }
        }

        view()->share('owner_socials', $links);
    }
}
