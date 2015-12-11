<?php

namespace App\Processors\PasswordFolder;

use App\Http\Presenters\PasswordFolder\PasswordPresenter;
use App\Http\Requests\PasswordFolder\PasswordRequest;
use App\Models\Password;
use App\Models\PasswordFolder;
use App\Processors\Processor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class PasswordProcessor extends Processor
{
    /**
     * @var Password
     */
    protected $password;

    /**
     * @var PasswordPresenter
     */
    protected $presenter;

    /**
     * @var Guard
     */
    protected $guard;

    /**
     * Constructor.
     *
     * @param Password          $password
     * @param PasswordPresenter $presenter
     * @param Guard             $guard
     */
    public function __construct(Password $password, PasswordPresenter $presenter, Guard $guard)
    {
        $this->password = $password;
        $this->presenter = $presenter;
        $this->guard = $guard;
    }

    /**
     * Displays all of the users passwords.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                $passwords = $this->presenter->table($folder->passwords()->getQuery());

                $navbar = $this->presenter->navbar();

                return view('pages.passwords.index', compact('passwords', 'navbar'));
            }
        }

        abort(404);
    }

    /**
     * Displays the form to create a password.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $form = $this->presenter->form($this->password);

        return view('pages.passwords.create', compact('form'));
    }

    /**
     * Creates a new user password.
     *
     * @param PasswordRequest $request
     *
     * @return Password|bool
     */
    public function store(PasswordRequest $request)
    {
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                $this->password->folder_id = $folder->getKey();
                $this->password->title = $request->input('title');
                $this->password->website = $request->input('website');
                $this->password->username = $request->input('username');
                $this->password->password = $request->input('password');
                $this->password->notes = $request->input('notes');

                if ($this->password->save()) {
                    return $this->password;
                }
            }
        }

        return false;
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
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                $password = $folder->passwords()->findOrFail($id);

                $form = $this->presenter->form($password, $viewing = true);

                return view('pages.passwords.show', compact('password', 'form'));
            }
        }

        abort(404);
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
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                $password = $folder->passwords()->findOrFail($id);

                $form = $this->presenter->form($password);

                return view('pages.passwords.edit', compact('password', 'form'));
            }
        }

        abort(404);
    }

    /**
     * Updates the users specified password.
     *
     * @param PasswordRequest $request
     * @param int|string      $id
     *
     * @return bool
     */
    public function update(PasswordRequest $request, $id)
    {
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                $password = $folder->passwords()->findOrFail($id);

                $password->title = $request->input('title', $password->title);
                $password->website = $request->input('website', $password->website);
                $password->username = $request->input('username', $password->username);
                $password->password = $request->input('password', $password->password);
                $password->notes = $request->input('notes', $password->notes);

                if ($password->save()) {
                    return $password;
                }
            }
        }

        return false;
    }

    /**
     * Deletes the specified user password record.
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function destroy($id)
    {
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                $password = $folder->passwords()->findOrFail($id);

                return $password->delete();
            }
        }

        return false;
    }
}
