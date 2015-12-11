<?php

namespace App\Jobs\Computer;

use App\Jobs\Job;
use App\Models\ComputerHardDisk;
use App\Models\ComputerHardDiskRecord;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateDiskRecord extends Job implements SelfHandling
{
    /**
     * @var ComputerHardDisk
     */
    protected $disk;

    /**
     * The current disks free space in bytes.
     *
     * @var int
     */
    protected $free = 0;

    /**
     * The current disk status.
     *
     * @var string|null
     */
    protected $status = null;

    /**
     * Constructor.
     *
     * @param ComputerHardDisk $disk
     * @param int              $free
     * @param string|null      $status
     */
    public function __construct(ComputerHardDisk $disk, $free = 0, $status = null)
    {
        $this->disk = $disk;
        $this->free = $free;
        $this->status = $status;
    }

    /**
     * Creates a new hard disk record for the current disk.
     *
     * @return bool|ComputerHardDiskRecord
     */
    public function handle()
    {
        $record = new ComputerHardDiskRecord();

        $record->disk_id = $this->disk->getKey();
        $record->free = $this->free;
        $record->status = $this->status;

        if ($record->save()) {
            return $record;
        }

        return false;
    }
}
