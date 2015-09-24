<?php

namespace App\Policies\ActiveDirectory;

use App\Models\User;
use App\Policies\Policy;

class UserPolicy extends Policy
{
    /**
     * Returns true / false if the specified user
     * can view all active directory computers.
     *
     * @param User $user
     *
     * @return bool
     */
    public function index(User $user)
    {
        return $user->is($this->admin()->name);
    }

    /**
     * Returns true / false if the specified user
     * can add active directory users.
     *
     * @param User $user
     *
     * @return bool
     */
    public function store(User $user)
    {
        return $user->is($this->admin()->name);
    }
}