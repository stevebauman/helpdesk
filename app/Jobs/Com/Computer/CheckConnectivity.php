<?php

namespace App\Jobs\Com\Computer;

use App\Jobs\Job;
use App\Models\Computer;
use Illuminate\Contracts\Bus\SelfHandling;
use Stevebauman\Wmi\Schemas\Namespaces;
use Stevebauman\Wmi\Wmi;

class CheckConnectivity extends Job implements SelfHandling
{
    /**
     * The computer to check.
     *
     * @var string
     */
    protected $computer;

    /**
     * The username to use to connect to the computer.
     *
     * @var string
     */
    protected $username = '';

    /**
     * The password to use to connect to the computer.
     *
     * @var string
     */
    protected $password = '';

    /**
     * Constructor.
     *
     * @param Computer $computer
     * @param string   $username
     * @param string   $password
     */
    public function __construct(Computer $computer, $username = '', $password = '')
    {
        $this->computer = $computer;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Tries to connect to the specified computer through WMI.
     *
     * @return bool
     */
    public function handle()
    {
        $wmi = new Wmi($this->computer->name, $this->username, $this->password);

        if ($wmi->connect(Namespaces::ROOT_CIMV2)) {
            return true;
        }

        return false;
    }
}
