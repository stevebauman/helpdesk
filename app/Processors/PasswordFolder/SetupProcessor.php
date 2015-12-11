<?php

namespace App\Processors\PasswordFolder;

use App\Http\Presenters\PasswordFolder\SetupPresenter;
use App\Http\Requests\PasswordFolder\SetupRequest;
use App\Jobs\PasswordFolder\Create as CreatePasswordFolder;
use App\Models\PasswordFolder;
use App\Processors\Processor;

class SetupProcessor extends Processor
{
    /**
     * @var PasswordFolder
     */
    protected $folder;

    /**
     * @var SetupPresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param PasswordFolder $folder
     * @param SetupPresenter $presenter
     */
    public function __construct(PasswordFolder $folder, SetupPresenter $presenter)
    {
        $this->folder = $folder;
        $this->presenter = $presenter;
    }

    /**
     * Displays the password setup form.
     *
     * @return \Illuminate\View\View
     */
    public function start()
    {
        $form = $this->presenter->form($this->folder);

        return view('pages.passwords.setup', compact('form'));
    }

    /**
     * @param SetupRequest $request
     *
     * @return bool|\App\Models\PasswordFolder
     */
    public function finish(SetupRequest $request)
    {
        $job = new CreatePasswordFolder($request->input('pin'));

        return $this->dispatch($job);
    }
}
