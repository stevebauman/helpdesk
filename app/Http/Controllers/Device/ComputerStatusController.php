<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Processors\Device\ComputerStatusProcessor;

class ComputerStatusController extends Controller
{
    /**
     * @var ComputerStatusProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param ComputerStatusProcessor $processor
     */
    public function __construct(ComputerStatusProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Checks the specified computers online status.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function check($id)
    {
        if ($this->processor->check($id)) {
            flash()->success('Success!', 'Successfully updated status.');

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was an issue updating this computers status. Please try again.');

            return redirect()->back();
        }
    }
}
