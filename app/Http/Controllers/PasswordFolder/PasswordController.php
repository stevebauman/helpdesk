<?php

namespace App\Http\Controllers\PasswordFolder;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordFolder\PasswordRequest;
use App\Processors\PasswordFolder\PasswordProcessor;

class PasswordController extends Controller
{
    /**
     * @var PasswordProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param PasswordProcessor $processor
     */
    public function __construct(PasswordProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays the current users passwords.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * Displays the form to create a password.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->processor->create();
    }

    /**
     * Creates a new user password.
     *
     * @param PasswordRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PasswordRequest $request)
    {
        if ($this->processor->store($request)) {
            flash()->success('Success!', 'Successfully created password record.');

            return redirect()->route('passwords.index');
        } else {
            flash()->success('Error!', 'There was an issue creating a password record. Please try again.');

            return redirect()->back();
        }
    }

    /**
     * Displays the users specified password.
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
     * Displays the edit form for the specified user password.
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
     * Updates the users specified password record.
     *
     * @param PasswordRequest $request
     * @param int|string      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PasswordRequest $request, $id)
    {
        if ($this->processor->update($request, $id)) {
            flash()->success('Success!', 'Successfully updated password record.');

            return redirect()->route('passwords.show', [$id]);
        } else {
            flash()->error('Error!', 'There was a problem updating this password record. Please try again.');

            return redirect()->route('passwords.edit', [$id]);
        }
    }

    /**
     * Deletes the specified user password record.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($this->processor->destroy($id)) {
            flash()->success('Success!', 'Successfully deleted password record.');

            return redirect()->route('passwords.index');
        } else {
            flash()->error('Error!', 'There was a problem deleting this password record. Please try again.');

            return redirect()->route('passwords.show', [$id]);
        }
    }
}
