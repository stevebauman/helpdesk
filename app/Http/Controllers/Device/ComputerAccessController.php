<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\ComputerAccessRequest;
use App\Processors\Device\ComputerAccessProcessor;

class ComputerAccessController extends Controller
{
    /**
     * @var ComputerAccessProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param ComputerAccessProcessor $processor
     */
    public function __construct(ComputerAccessProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays the page to edit the specified computers settings.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->processor->edit($id);
    }

    /**
     * Updates the specified computers settings.
     *
     * @param ComputerAccessRequest $request
     * @param int|string            $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ComputerAccessRequest $request, $id)
    {
        if ($this->processor->update($request, $id)) {
            flash()->success('Success!', 'Successfully updated settings.');

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was an issue updating settings. Please try again.');

            return redirect()->back();
        }
    }
}
