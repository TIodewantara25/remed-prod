<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('api_token')) {//Mengecek apakah api yang dikirimkan memiliki headers dengan key api_token
                //jika iya, maka akan di retrun  berupa data user berdasarkan kolom token yang mana value dari tokenya disamakan denangn value di headers mengunakn key_token

                return User::where('token', $request->header('api_token'))->first();
            }
        });
    }
}