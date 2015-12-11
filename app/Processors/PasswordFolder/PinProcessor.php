<?php

namespace App\Processors\PasswordFolder;

use App\Http\Presenters\PasswordFolder\PinPresenter;
use App\Http\Requests\PasswordFolder\ChangePinRequest;
use App\Models\PasswordFolder;
use App\Processors\Processor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class PinProcessor extends Processor
{
    /**
     * @var PinPresenter
     */
    protected $presenter;

    /**
     * @var Guard
     */
    protected $guard;

    /**
     * Constructor.
     *
     * @param PinPresenter $presenter
     * @param Guard        $guard
     */
    public function __construct(PinPresenter $presenter, Guard $guard)
    {
        $this->presenter = $presenter;
        $this->guard = $guard;
    }

    /**
     * Displays the form to change the users password folder PIN.
     *
     * @return \Illuminate\View\View
     */
    public function change()
    {
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                $form = $this->presenter->form();

                return view('pages.passwords.pin.change', compact('form'));
            }
        }

        abort(404);
    }

    /**
     * Changes the current users password folder PIN.
     *
     * @param ChangePinRequest $request
     *
     * @return bool
     */
    public function update(ChangePinRequest $request)
    {
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                return $folder->changePin($request);
            }
        }

        return false;
    }
}
