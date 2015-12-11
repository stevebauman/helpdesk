<?php

namespace App\Processors\Device;

use Adldap\Contracts\Adldap;
use Adldap\Models\Computer as AdComputer;
use App\Http\Presenters\Device\ComputerPresenter;
use App\Http\Requests\Device\ComputerRequest;
use App\Jobs\ActiveDirectory\ImportComputer;
use App\Jobs\Computer\Create;
use App\Models\Computer;
use App\Models\ComputerType;
use App\Models\OperatingSystem;
use App\Processors\Processor;

class ComputerProcessor extends Processor
{
    /**
     * @var Computer
     */
    protected $computer;

    /**
     * @var Adldap
     */
    protected $adldap;

    /**
     * @var ComputerPresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param Computer          $computer
     * @param Adldap            $adldap
     * @param ComputerPresenter $presenter
     */
    public function __construct(Computer $computer, Adldap $adldap, ComputerPresenter $presenter)
    {
        $this->computer = $computer;
        $this->adldap = $adldap;
        $this->presenter = $presenter;
    }

    /**
     * Displays all computers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->authorize($this->computer);

        $computers = $this->presenter->table($this->computer);

        $navbar = $this->presenter->navbar();

        return view('pages.devices.computers.index', compact('computers', 'navbar'));
    }

    /**
     * Displays the form to create a computer.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize($this->computer);

        $form = $this->presenter->form($this->computer);

        return view('pages.devices.computers.create', compact('form'));
    }

    /**
     * Creates a new computer.
     *
     * @param ComputerRequest $request
     *
     * @return bool|Computer
     */
    public function store(ComputerRequest $request)
    {
        $this->authorize($this->computer);

        // If the user is looking to import the computer from active
        // directory, then we'll try to find the computer by
        // the given name and dispatch the import job.
        if ($request->input('active_directory')) {
            return $this->storeFromActiveDirectory($request);
        } else {
            return $this->storeFromRequest($request);
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
        $this->authorize($this->computer);

        $with = [
            'os',
            'type',
            'users',
            'access',
        ];

        $computer = $this->computer->with($with)->findOrFail($id);

        return view('pages.devices.computers.show.details', compact('computer'));
    }

    /**
     * Displays the form for the specified computer.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $this->authorize($this->computer);

        $computer = $this->computer->findOrFail($id);

        $form = $this->presenter->form($computer);

        return view('pages.devices.computers.edit', compact('form'));
    }

    /**
     * Updates the specified computer.
     *
     * @param ComputerRequest $request
     * @param int|string      $id
     *
     * @return bool
     */
    public function update(ComputerRequest $request, $id)
    {
        $this->authorize($this->computer);

        $computer = $this->computer->findOrFail($id);

        $os = OperatingSystem::findOrFail($request->input('os'));
        $type = ComputerType::findOrFail($request->input('type'));

        $computer->name = $request->input('name');
        $computer->model = $request->input('model');
        $computer->description = $request->input('description');
        $computer->os_id = $os->getKey();
        $computer->type_id = $type->getKey();

        if ($computer->save()) {
            return $computer;
        }

        return false;
    }

    /**
     * Deletes the specified computer.
     *
     * @param string $id
     *
     * @return bool
     */
    public function destroy($id)
    {
        $this->authorize($this->computer);

        $computer = $this->computer->findOrFail($id);

        return $computer->delete();
    }

    /**
     * Creates a new computer from a request.
     *
     * @param ComputerRequest $request
     *
     * @return Computer
     */
    protected function storeFromRequest(ComputerRequest $request)
    {
        $os = OperatingSystem::findOrFail($request->input('os'));
        $type = ComputerType::findOrFail($request->input('type'));

        $name = $request->input('name');
        $model = $request->input('model');
        $description = $request->input('description');

        return $this->dispatch(new Create($type->getKey(), $os->getKey(), $name, $description, $model));
    }

    /**
     * Creates a new computer from active directory.
     *
     * @param ComputerRequest $request
     *
     * @return bool|Computer
     */
    protected function storeFromActiveDirectory(ComputerRequest $request)
    {
        $computer = $this->adldap->computers()->find($request->input('name'));

        if ($computer instanceof AdComputer) {
            return $this->dispatch(new ImportComputer($computer));
        }

        return false;
    }
}
