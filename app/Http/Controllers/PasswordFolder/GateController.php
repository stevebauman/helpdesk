<?php

namespace App\Http\Controllers\PasswordFolder;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordFolder\LockRequest;
use App\Http\Requests\PasswordFolder\UnlockRequest;
use App\Processors\PasswordFolder\GateProcessor;

class GateController extends Controller
{
    /**
     * @var GateProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param GateProcessor $processor
     */
    public function __construct(GateProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays the password folder PIN gate.
     *
     * @return \Illuminate\View\View
     */
    public function gate()
    {
        return $this->processor->gate();
    }

    /**
     * Unlocks a users password folder.
     *
     * @param UnlockRequest $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function unlock(UnlockRequest $request)
    {
        if ($this->processor->unlock($request)) {
            flash()->success('Success!', 'Successfully entered password folder');

            return redirect()->route('passwords.index');
        } else {
            return redirect()->route('passwords.gate')->withErrors(['pin' => 'Incorrect PIN']);
        }
    }

    /**
     * Locks the current users password folder.
     *
     * @param LockRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lock(LockRequest $request)
    {
        if ($this->processor->lock($request)) {
            flash()->success('Success!', 'Successfully locked passwords.');

            return redirect()->route('welcome.index');
        } else {
            flash()->error('Error!', 'There was a problem locking passwords. Please try again.');

            return redirect()->route('welcome.index');
        }
    }
}
