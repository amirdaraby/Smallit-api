<?php

namespace App\Providers;

use App\Models\Batch;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
//         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define("batch-owner", function (User $user, Batch $batch){
             return $user->id === $batch->user_id ? Response::allow() : Response::deny();
        });

        Gate::define("shorturl-owner", function (User $user , ShortUrl $shortUrl){
            return $user->id === $shortUrl->user_id ? Response::allow() : Response::deny();
        });

        Gate::define("url-owner", function (User $user , Url $url){
            return $user->id === $url->user_id ? Response::allow() : Response::deny();
        });

    }
}
