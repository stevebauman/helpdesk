<?php

namespace App\Jobs\Computer;

use App\Jobs\Job;
use App\Models\Computer;
use App\Models\ComputerHardDisk;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateDisk extends Job implements SelfHandling
{
    /**
     * @var Computer
     */
    protected $computer;

    /**
     * The disk name.
     *
     * @var string
     */
    protected $name;

    /**
     * The disk capacity in bytes.
     *
     * @var int
     */
    protected $size = 0;

    /**
     * The disk install date.
     *
     * @var null|string
     */
    protected $installed = null;

    /**
     * The disk description.
     *
     * @var null|string
     */
    protected $description = null;

    /**
     * Constructor.
     *
     * @param Computer    $computer
     * @param string      $name
     * @param int         $size
     * @param string|null $installed
     * @param string|null $description
     */
    public function __construct(Computer $computer, $name, $size = 0, $installed = null, $description = null)
    {
        $this->computer = $computer;
        $this->name = $name;
        $this->size = $size;
        $this->installed = $installed;
        $this->description = $description;
    }

    /**
     * Creates a new computer hard disk.
     *
     * @return bool|ComputerHardDisk
     */
    public function handle()
    {
        $disk = ComputerHardDisk::firstOrNew([
            'computer_id' => $this->computer->getKey(),
            'name'        => $this->name,
        ]);

        $disk->size = $this->size;
        $disk->installed = $this->installed;
        $disk->description = $this->description;

        if ($disk->save()) {
            return $disk;
        }

        return false;
    }
}
