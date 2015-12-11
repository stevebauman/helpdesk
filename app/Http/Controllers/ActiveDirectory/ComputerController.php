<?php

namespace App\Http\Controllers\ActiveDirectory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActiveDirectory\ComputerImportRequest;
use App\Models\Computer;
use App\Processors\ActiveDirectory\ComputerProcessor;
use Illuminate\Http\Request;

class ComputerController extends Controller
{
    /**
     * Constructor.
     *
     * @param ComputerProcessor $processor
     */
    public function __construct(ComputerProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays all active directory computers.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return $this->processor->index($request);
    }

    /**
     * Imports an active directory computer.
     *
     * @param ComputerImportRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ComputerImportRequest $request)
    {
        $computer = $this->processor->store($request);

        if ($computer instanceof Computer) {
            $name = $computer->name;

            flash()->success('Success!', "Successfully added computer $name.");

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was an error adding this computer. Please try again.');

            return redirect()->back();
        }
    }

    /**
     * Imports all active directory computers.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAll()
    {
        $added = count($this->processor->storeAll());

        if ($added > 0) {
            flash()->success('Success!', "Successfully added $added computer(s).");

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was an error adding all computers. Please try again.');

            return redirect()->back();
        }
    }
}
