<?php

namespace App\Jobs\Users;

use App\Jobs\Job;
use App\Models\User;
use Illuminate\Contracts\Bus\SelfHandling;

class Create extends Job implements SelfHandling
{
    /**
     * The users email;.
     *
     * @var string
     */
    protected $email;

    /**
     * The users password.
     *
     * @var string
     */
    protected $password;

    /**
     * The users full name.
     *
     * @var null|string
     */
    protected $fullName;

    /**
     * Constructor.
     *
     * @param string $email
     * @param string $password
     * @param string $fullName
     */
    public function __construct($email, $password, $fullName = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->fullName = $fullName;
    }

    /**
     * Creates a new user.
     *
     * @param User $model
     *
     * @return User|bool
     */
    public function handle(User $model)
    {
        $exists = $model->where('email', $this->email)->first();

        if (is_null($exists)) {
            $user = $model->newInstance();

            $user->email = $this->email;
            $user->fullname = $this->fullName;

            if ($user->hasSetMutator('password')) {
                // If the user model has a password set mutator, we'll assume
                // the developer is encrypting the passwords themselves.
                $user->password = $this->password;
            } else {
                // Otherwise we'll encrypt the password.
                $user->password = bcrypt($this->password);
            }

            $user->save();

            return $user;
        }

        return false;
    }
}
