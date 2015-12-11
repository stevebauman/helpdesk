<?php

namespace App\Http\Controllers\ActiveDirectory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActiveDirectory\UserImportRequest;
use App\Http\Requests\ActiveDirectory\UserRequest;
use App\Models\User;
use App\Processors\ActiveDirectory\UserProcessor;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param UserProcessor $processor
     */
    public function __construct(UserProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays all active directory users.
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
     * Displays the.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->processor->create();
    }

    /**
     * Creates a new active directory user.
     *
     * @param UserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        if ($this->processor->store($request)) {
            flash()->success('Success!', 'Successfully created active directory user.');

            return redirect()->route('active-directory.users.index');
        } else {
            flash()->success('Error!', 'There was an issue creating this active directory user. Please try again.');

            return redirect()->route('active-directory.users.create');
        }
    }

    /**
     * Displays information about the specified user.
     *
     * @param string $username
     *
     * @return \Illuminate\View\View
     */
    public function show($username)
    {
        return $this->processor->show($username);
    }

    /**
     * Displays the form for editing the specified user.
     *
     * @param string $username
     *
     * @return \Illuminate\View\View
     */
    public function edit($username)
    {
        return $this->processor->edit($username);
    }

    /**
     * Updates the specified active directory user.
     *
     * @param UserRequest $request
     * @param string      $username
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, $username)
    {
        if ($this->processor->update($request, $username)) {
            flash()->success('Success!', 'Successfully updated active directory user.');

            return redirect()->route('active-directory.users.show', [$username]);
        } else {
            flash()->error('Error!', 'There was an issue updating this active directory user.');

            return redirect()->route('active-directory.users.edit', [$username]);
        }
    }

    /**
     * Imports an active directory user.
     *
     * @param UserImportRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(UserImportRequest $request)
    {
        $user = $this->processor->import($request);

        if ($user instanceof User) {
            $name = $user->fullname;

            flash()->success('Success!', "Successfully added user $name.");

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was a problem adding a user. Please try again.');

            return redirect()->back();
        }
    }
}
