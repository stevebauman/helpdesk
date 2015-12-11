<?php

namespace App\Http\Controllers\PasswordFolder;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordFolder\SetupRequest;
use App\Processors\PasswordFolder\SetupProcessor;

class SetupController extends Controller
{
    /**
     * @var SetupProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param SetupProcessor $processor
     */
    public function __construct(SetupProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays the password setup form.
     *
     * @return \Illuminate\View\View
     */
    public function start()
    {
        return $this->processor->start();
    }

    /**
     * Finishes the users password folder setup.
     *
     * @param SetupRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish(SetupRequest $request)
    {
        if ($this->processor->finish($request)) {
            flash()->success('Success!', 'Successfully setup passwords.');

            return redirect()->route('passwords.gate');
        } else {
            flash()->success('Error!', 'There was an error setting up passwords. Please try again.');

            return redirect()->back();
        }
    }

    /**
     * Displays the invalid setup page.
     *
     * @return \Illuminate\View\View
     */
    public function invalid()
    {
        return view('pages.passwords.invalid');
    }
}
