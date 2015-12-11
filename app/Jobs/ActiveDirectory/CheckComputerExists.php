<?php

namespace App\Jobs\ActiveDirectory;

use Adldap\Contracts\Adldap;
use Adldap\Models\Computer;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class CheckComputerExists extends Job implements SelfHandling
{
    /**
     * The computers name to check.
     *
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name = '')
    {
        $this->name = $name;
    }

    /**
     * Returns true / false if the specified
     * computer exists in active directory.
     *
     * @param Adldap $adldap
     *
     * @return bool
     */
    public function handle(Adldap $adldap)
    {
        $computer = $adldap->computers()->find($this->name);

        if ($computer instanceof Computer) {
            return true;
        }

        return false;
    }
}
