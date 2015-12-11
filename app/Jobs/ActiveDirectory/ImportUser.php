<?php

namespace App\Jobs\ActiveDirectory;

use Adldap\Models\User as AdUser;
use App\Jobs\Job;
use App\Jobs\Users\Create as CreateUser;
use App\Models\User;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ImportUser extends Job implements SelfHandling
{
    use DispatchesJobs;

    /**
     * @var AdUser
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param AdUser $user
     */
    public function __construct(AdUser $user)
    {
        $this->user = $user;
    }

    /**
     * Imports an active directory user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function handle(User $user)
    {
        $exists = $user->where('email', $this->user->getEmail())->first();

        if (!$exists) {
            $email = $this->user->getEmail();
            $password = str_random(40);
            $fullName = $this->user->getName();

            return $this->dispatch(new CreateUser($email, $password, $fullName));
        }

        return false;
    }
}
