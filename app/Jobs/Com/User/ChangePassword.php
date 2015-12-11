<?php

namespace App\Jobs\Com\User;

use Adldap\Models\User;
use App\Jobs\Job;
use COM;
use COM_EXCEPTION;
use Illuminate\Contracts\Bus\SelfHandling;

class ChangePassword extends Job implements SelfHandling
{
    /**
     * The current COM instance.
     *
     * @var resource
     */
    protected $com;

    /**
     * @var User
     */
    protected $user;

    /**
     * The new password of the specified user.
     *
     * @var string
     */
    protected $password;

    /**
     * The COM command to instantiate with.
     *
     * @var string
     */
    protected $command = 'LDAP:';

    /**
     * The server to connect to.
     *
     * @var string
     */
    protected $server = '';

    /**
     * The account suffix to use when connecting to the server.
     *
     * @var string
     */
    protected $adminAccountSuffix = '';

    /**
     * The username to use when connecting to the server.
     *
     * @var string
     */
    protected $adminUsername = '';

    /**
     * The password to use when connecting to the server.
     *
     * @var string
     */
    protected $adminPassword = '';

    /**
     * Constructor.
     *
     * @param User   $user
     * @param string $password
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->com = new COM($this->command);

        $default = [
            'domain_controllers' => [],
            'admin_username'     => null,
            'admin_password'     => null,
            'account_suffix'     => null,
        ];

        $config = config('adldap.connection_settings', $default);

        $this->server = $config['domain_controllers'][0];
        $this->adminUsername = $config['admin_username'];
        $this->adminPassword = $config['admin_password'];
        $this->adminAccountSuffix = $config['account_suffix'];
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        try {
            $user = $this->getDsObject($this->user->getDn());

            $user->SetPassword($this->password);

            $user->SetInfo();
        } catch (COM_EXCEPTION $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns a COM object using the specified user distinguished name.
     *
     * @param string $dn
     *
     * @return mixed
     */
    protected function getDsObject($dn)
    {
        return $this->com->OpenDSObject('LDAP://'.$this->server.'/'.$dn, $this->adminUsername.$this->adminAccountSuffix, $this->adminPassword, 1);
    }
}
