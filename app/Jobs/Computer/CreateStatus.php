<?php

namespace App\Jobs\Computer;

use App\Jobs\Job;
use App\Models\Computer;
use App\Models\ComputerStatus;
use Illuminate\Contracts\Bus\SelfHandling;

class CreateStatus extends Job implements SelfHandling
{
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
     * Creates a new computer status.
     *
     * @param ComputerStatus $status
     *
     * @return ComputerStatus|bool
     */
    public function handle(ComputerStatus $status)
    {
        $status->computer_id = $this->computer->getKey();

        $latency = $this->computer->ping();

        if ($latency) {
            $status->online = true;
            $status->latency = $latency;
        }

        if ($status->save()) {
            return $status;
        }

        return false;
    }
}
