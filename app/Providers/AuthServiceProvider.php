<?php

namespace App\Providers;

use App\Policies\GlobalPolicy;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Orchestra\Contracts\Foundation\Foundation;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application authentication / authorization services.
     *
     * @param Gate       $gate
     * @param Foundation $foundation
     *
     * @return void
     */
    public function boot(Gate $gate, Foundation $foundation)
    {
        $this->policies = config('authorization.policies');

        parent::registerPolicies($gate);

        // Register global policies.
        foreach (get_class_methods(GlobalPolicy::class) as $method) {
            $callback = sprintf('%s@%s', GlobalPolicy::class, $method);

            $gate->define($method, $callback);
        }

        $this->app->booted(function () use ($foundation) {
            if ($foundation->installed()) {
                // If foundation is installed, we can register the
                // Authorization Service Provider.
                $this->app->register(AuthorizationServiceProvider::class);
            }
        });
    }
}
