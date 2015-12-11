<?php

namespace App\Processors\Device;

use App\Http\Presenters\Device\DrivePresenter;
use App\Http\Requests\Device\DriveRequest;
use App\Models\Drive;
use App\Processors\Processor;

class DriveProcessor extends Processor
{
    /**
     * @var Drive
     */
    protected $drive;

    /**
     * @var DrivePresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param Drive          $drive
     * @param DrivePresenter $presenter
     */
    public function __construct(Drive $drive, DrivePresenter $presenter)
    {
        $this->drive = $drive;
        $this->presenter = $presenter;
    }

    /**
     * Displays all drives.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $drives = $this->presenter->table($this->drive);

        $navbar = $this->presenter->navbar();

        return view('pages.devices.drives.index', compact('drives', 'navbar'));
    }

    /**
     * Displays the form for creating a new drive.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $form = $this->presenter->form($this->drive);

        return view('pages.devices.drives.create', compact('form'));
    }

    /**
     * Creates a new drive.
     *
     * @param DriveRequest $request
     *
     * @return bool
     */
    public function store(DriveRequest $request)
    {
        $drive = $this->drive->newInstance();

        $drive->name = $request->input('name');
        $drive->path = $request->input('path');
        $drive->is_network = $request->input('is_network', false);

        return $drive->save();
    }

    /**
     * Displays the specified drive.
     *
     * @param int|string  $id
     * @param string|null $path
     *
     * @return \Illuminate\View\View|bool
     */
    public function show($id, $path = null)
    {
        $drive = $this->drive->findOrFail($id);

        $accounts = $drive->accounts($path);

        if ($accounts) {
            $items = $this->presenter->tableItems($drive, $path);

            return view('pages.devices.drives.show', compact('drive', 'accounts', 'items'));
        }

        return false;
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
        $drive = $this->drive->findOrFail($id);

        $form = $this->presenter->form($drive);

        return view('pages.devices.drives.edit', compact('form'));
    }

    /**
     * Updates the specified drive.
     *
     * @param DriveRequest $request
     * @param int|string   $id
     *
     * @return bool
     */
    public function update(DriveRequest $request, $id)
    {
        $drive = $this->drive->findOrFail($id);

        $drive->name = $request->input('name', $drive->name);
        $drive->path = $request->input('path', $drive->path);
        $drive->is_network = $request->input('is_network', $drive->is_network);

        return $drive->save();
    }

    /**
     * Deletes the specified drive.
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function destroy($id)
    {
        $drive = $this->drive->findOrFail($id);

        return $drive->delete();
    }
}
