<?php

namespace App\Jobs\Computer;

use App\Jobs\Job;
use App\Models\Computer;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Create extends Job implements SelfHandling
{
    use DispatchesJobs;

    /**
     * The computers type ID.
     *
     * @var int|string
     */
    protected $typeId;

    /**
     * The computers os ID.
     *
     * @var int|string
     */
    protected $osId;

    /**
     * The computers name.
     *
     * @var string
     */
    protected $name;

    /**
     * The computers description.
     *
     * @var null|string
     */
    protected $description = null;

    /**
     * The computers model.
     *
     * @var null|string
     */
    protected $model = null;

    /**
     * The computers distinguished name.
     *
     * @var null|string
     */
    protected $dn = null;

    /**
     * Constructor.
     *
     * @param int|string  $typeId
     * @param int|string  $osId
     * @param string      $name
     * @param null|string $description
     * @param null|string $dn
     * @param null|string $model
     */
    public function __construct($typeId, $osId, $name, $description = null, $dn = null, $model = null)
    {
        $this->typeId = $typeId;
        $this->osId = $osId;
        $this->name = $name;
        $this->description = $description;
        $this->dn = $dn;
        $this->model = $model;
    }

    /**
     * Creates and returns a new Computer.
     *
     * @param Computer $model
     *
     * @return bool|Computer
     */
    public function handle(Computer $model)
    {
        // Verify that the computer doesn't exist already
        $exists = $model->where('dn', $this->dn)->first();

        if (is_null($exists)) {
            $computer = $model->newInstance();

            $computer->type_id = $this->typeId;
            $computer->os_id = $this->osId;
            $computer->name = $this->name;
            $computer->description = $this->description;
            $computer->dn = $this->dn;
            $computer->model = $this->model;

            if ($computer->save()) {
                $this->dispatch(new CreateAccess($computer));

                return $computer;
            }
        }

        return false;
    }
}
