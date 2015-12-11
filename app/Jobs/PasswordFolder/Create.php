<?php

namespace App\Jobs\PasswordFolder;

use App\Jobs\Job;
use App\Models\PasswordFolder;
use Illuminate\Contracts\Bus\SelfHandling;

class Create extends Job implements SelfHandling
{
    /**
     * The password folder pin.
     *
     * @var int|string
     */
    protected $pin;

    /**
     * Constructor.
     *
     * @param $pin
     */
    public function __construct($pin)
    {
        $this->pin = $pin;
    }

    /**
     * Create a new user password folder.
     *
     * @param PasswordFolder $folder
     *
     * @return PasswordFolder|bool
     */
    public function handle(PasswordFolder $folder)
    {
        $folder->user_id = auth()->user()->getKey();
        $folder->locked = true;
        $folder->uuid = uuid();
        $folder->pin = $this->pin;

        if ($folder->save()) {
            return $folder;
        }

        return false;
    }
}
