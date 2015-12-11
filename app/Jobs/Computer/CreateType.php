<?php

namespace App\Jobs\Computer;

use App\Jobs\Job;
use App\Models\ComputerType;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Str;

class CreateType extends Job implements SelfHandling
{
    /**
     * The name of the type.
     *
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Sets the computer type name from
     * the specified operating system.
     *
     * @param string $os
     *
     * @return CreateType
     */
    public function fromOs($os)
    {
        if (Str::contains($os, 'Windows Server')) {
            $this->name = 'Server';
        } elseif (Str::contains($os, 'Windows')) {
            $this->name = 'Workstation';
        } else {
            $this->name = 'Unknown';
        }

        return $this;
    }

    /**
     * Creates a computer type.
     *
     * @return ComputerType
     */
    public function handle()
    {
        $name = $this->name;

        return ComputerType::firstOrCreate(compact('name'));
    }
}
