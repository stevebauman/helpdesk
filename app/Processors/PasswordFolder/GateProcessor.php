<?php

namespace App\Processors\PasswordFolder;

use App\Http\Presenters\PasswordFolder\GatePresenter;
use App\Http\Requests\PasswordFolder\LockRequest;
use App\Http\Requests\PasswordFolder\UnlockRequest;
use App\Models\PasswordFolder;
use App\Processors\Processor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

class GateProcessor extends Processor
{
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var GatePresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param GatePresenter $presenter
     * @param Guard         $guard
     */
    public function __construct(GatePresenter $presenter, Guard $guard)
    {
        $this->guard = $guard;
        $this->presenter = $presenter;
    }

    /**
     * Displays the password folder PIN gate.
     *
     * @return \Illuminate\View\View
     */
    public function gate()
    {
        $form = $this->presenter->form();

        return view('pages.passwords.gate', compact('form'));
    }

    /**
     * Unlocks the current users password folder.
     *
     * @param UnlockRequest $request
     *
     * @return bool
     */
    public function unlock(UnlockRequest $request)
    {
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                return $folder->unlock($request);
            }
        }

        return false;
    }

    /**
     * Locks a users password folder.
     *
     * @param LockRequest $request
     *
     * @return bool
     */
    public function lock(LockRequest $request)
    {
        $user = $this->guard->user();

        if ($user instanceof Authenticatable) {
            $folder = $user->passwordFolder;

            if ($folder instanceof PasswordFolder) {
                return $folder->lock($request);
            }
        }

        return false;
    }
}
