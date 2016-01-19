<?php

namespace App\Jobs\Com\Computer;

use App\Jobs\Job;
use App\Models\Computer;
use App\Models\ComputerAccess;
use Stevebauman\Wmi\Wmi;

abstract class AbstractComputerJob extends Job
{
    /**
     * @var Computer
     */
    protected $computer;

    /**
     * @var Wmi
     */
    protected $wmi;

    /**
     * The username used to connect to the computer.
     *
     * @var string
     */
    protected $username;

    /**
     * The password used to connect to the computer.
     *
     * @var string
     */
    protected $password;

    /**
     * Constructor.
     *
     * @param Computer $computer
     */
    public function __construct(Computer $computer)
    {
        $this->computer = $computer;

        list($username, $password) = $this->getCredentials();

        $this->wmi = new Wmi($this->computer->name, $username, $password);

        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Returns credentials for the specified computer.
     *
     * @return array
     */
    protected function getCredentials()
    {
        $access = $this->computer->getWmiAccess();

        $username = null;
        $password = null;

        if ($access instanceof ComputerAccess) {
            $username = $access->getWmiUsername();
            $password = $access->getWmiPassword();
        }

        // Check if the access record contains a username and password.
        if (is_null($username) && is_null($password)) {
            $prefix = 'adldap.connection_settings';

            $username = config("$prefix.admin_username").config("$prefix.account_suffix");
            $password = config("$prefix.admin_password");
        }

        return [$username, $password];
    }
}