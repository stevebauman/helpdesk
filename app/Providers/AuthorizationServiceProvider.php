<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Orchestra\Contracts\Foundation\Foundation;
use Orchestra\Model\Role;
use Orchestra\Support\Facades\ACL;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Foundation $foundation
     *
     * @return void
     */
    public function boot(Foundation $foundation)
    {
        $policies = config('authorization.policies');

        $memory = $foundation->memory();

        $this->app->booted(function () use ($policies, $memory) {
            foreach ($policies as $policy) {
                $acl = ACL::make($policy);

                $acl->attach($memory);

                $actions = [];

                if (property_exists($policy, 'actions') && $policy = app($policy)) {
                    $actions = array_merge($actions, $policy->actions);
                }

                $roles = Role::lists('name')->all();

                $acl->roles()->attach($roles);

                $acl->actions()->attach($actions);

                $admin = Role::admin();

                if ($admin instanceof Role) {
                    $acl->allow($admin->name, $actions);
                }
            }
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
