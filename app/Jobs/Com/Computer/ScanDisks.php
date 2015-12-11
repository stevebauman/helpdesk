<?php

namespace App\Jobs\Com\Computer;

use App\Jobs\Computer\CreateDisk;
use App\Jobs\Computer\CreateDiskRecord;
use App\Jobs\Job;
use App\Models\Computer;
use App\Models\ComputerHardDisk;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Stevebauman\Wmi\ConnectionInterface;
use Stevebauman\Wmi\Models\Variants\HardDisk;
use Stevebauman\Wmi\Schemas\Namespaces;
use Stevebauman\Wmi\Wmi;

class ScanDisks extends Job implements SelfHandling
{
    use DispatchesJobs;

    /**
     * @var Computer
     */
    protected $computer;

    /**
     * Constructor.
     *
     * @param Computer $computer
     */
    public function __construct(Computer $computer)
    {
        $this->computer = $computer;
    }

    /**
     * Scans the current computers hard disks and creates a record of them.
     *
     * @return bool|array
     */
    public function handle()
    {
        // Check if the computer can be accessed by WMI.
        if ($this->computer->getWmiAccess()) {
            // Check if the access record contains a username and password.
            if ($this->computer->access->wmi_username && $this->computer->access->wmi_password) {
                $username = $this->computer->access->wmi_username;
                $password = $this->computer->access->wmi_password;
            } else {
                $prefix = 'adldap.connection_settings';

                $username = config("$prefix.admin_username").config("$prefix.account_suffix");
                $password = config("$prefix.admin_password");
            }

            // Create a new WMI instance on the computer.
            $wmi = new Wmi($this->computer->name, $username, $password);

            try {
                // Try to connect to the machine.
                $connection = $wmi->connect(Namespaces::ROOT_CIMV2);

                // Check that we're connected to the machine.
                if ($connection instanceof ConnectionInterface) {
                    // Get the machines hard disks.
                    $disks = $connection->hardDisks()->get();

                    $added = [];

                    foreach ($disks as $disk) {
                        // Go through each disk and create a new
                        // disk record for the current computer.
                        if ($disk instanceof HardDisk) {
                            // Make sure we skip drive type 5 since it's a CD-ROM drive
                            if ($disk->getDriveType() !== 5) {
                                $job = new CreateDisk($this->computer, $disk->getName(), $disk->getSize(), $disk->getInstallDate(), $disk->getDescription());

                                $hd = $this->dispatch($job);

                                if ($hd instanceof ComputerHardDisk) {
                                    // If a hard disk is successfully created, we'll
                                    // dispatch the job to create a new disk record.
                                    $this->dispatch(new CreateDiskRecord($hd, $disk->getFreeSpace(), $disk->getStatus()));
                                }

                                $added[] = $hd;
                            }
                        }
                    }

                    return $added;
                }
            } catch (\COM_EXCEPTION $e) {
                //
            }
        }

        return false;
    }
}
