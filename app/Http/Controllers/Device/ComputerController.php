<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\ComputerRequest;
use App\Models\Computer;
use App\Processors\Device\ComputerProcessor;

class ComputerController extends Controller
{
    /**
     * @var ComputerProcessor
     */
    protected $processor;

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
     * Displays all computers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * Show the form for creating a new computer.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->processor->create();
    }

    /**
     * Creates a computer.
     *
     * @param ComputerRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ComputerRequest $request)
    {
        $computer = $this->processor->store($request);

        if ($computer instanceof Computer) {
            flash()->success('Success!', 'Successfully created computer.');

            return redirect()->route('devices.computers.index');
        } else {
            flash()->error('Error!', 'There was an issue creating a computer. Please try again.');

            return redirect()->route('devices.computers.create');
        }
    }

    /**
     * Displays the specified computer.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->processor->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->processor->edit($id);
    }

    /**
     * Updates the specified computer.
     *
     * @param ComputerRequest $request
     * @param int|string      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ComputerRequest $request, $id)
    {
        if ($this->processor->update($request, $id)) {
            flash()->success('Success!', 'Successfully updated computer.');

            return redirect()->route('devices.computers.show', [$id]);
        } else {
            flash()->error('Error!', 'There was an issue updating this computer. Please try again.');

            return redirect()->route('devices.computers.edit', [$id]);
        }
    }

    /**
     * Deletes the specified computer.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($this->processor->destroy($id)) {
            flash()->success('Success!', 'Successfully deleted computer.');

            return redirect()->route('devices.computers.index');
        } else {
            flash()->error('Error!', 'There was an issue deleting this computer. Please try again.');

            return redirect()->route('devices.computers.show', [$id]);
        }
    }
}
