<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DriveRequest;
use App\Processors\Device\DriveProcessor;
use Illuminate\Contracts\View\View;

class DriveController extends Controller
{
    /**
     * @var DriveProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param DriveProcessor $processor
     */
    public function __construct(DriveProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays all drives.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * Displays the form for creating a new drive.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->processor->create();
    }

    /**
     * Creates a new drive.
     *
     * @param DriveRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DriveRequest $request)
    {
        if ($this->processor->store($request)) {
            flash()->success('Success!', 'Successfully added drive.');

            return redirect()->route('devices.drives.index');
        } else {
            flash()->error('Error!', 'There was an issue adding this drive. Please try again.');

            return redirect()->route('devices.drives.create');
        }
    }

    /**
     * Displays the specified drive.
     *
     * @param int|string  $id
     * @param string|null $path
     *
     * @return \Illuminate\View\View
     */
    public function show($id, $path = null)
    {
        $view = $this->processor->show($id, $path);

        if ($view instanceof View) {
            return $view;
        }

        $message = 'It looks like were having issues displaying the contents of this drive. Please try again.';

        flash()->error('Error!', $message);

        return redirect()->route('devices.drives.index');
    }

    /**
     * Displays the form for editing the specified drive.
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
     * Updates the specified drive.
     *
     * @param DriveRequest $request
     * @param int|string   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DriveRequest $request, $id)
    {
        if ($this->processor->update($request, $id)) {
            flash()->success('Success!', 'Successfully updated drive.');

            return redirect()->route('devices.drives.show', [$id]);
        } else {
            flash()->error('Error!', 'There was an issue updating this drive. Please try again.');

            return redirect()->route('devices.drives.edit', [$id]);
        }
    }

    /**
     * Deletes the specified drive.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($this->processor->destroy($id)) {
            flash()->success('Success!', 'Successfully deleted drive.');

            return redirect()->route('devices.drives.index');
        } else {
            flash()->error('Error!', 'There was an issue deleting this drive. Please try again.');

            return redirect()->route('devices.drives.show', [$id]);
        }
    }
}
