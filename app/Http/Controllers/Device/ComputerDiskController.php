<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Processors\Device\ComputerDiskProcessor;

class ComputerDiskController extends Controller
{
    /**
     * @var ComputerDiskProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param ComputerDiskProcessor $processor
     */
    public function __construct(ComputerDiskProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays the specified computers hard disks.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        return $this->processor->index($id);
    }

    /**
     * Synchronizes the specified computers hard disks.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function synchronize($id)
    {
        $added = $this->processor->synchronize($id);

        if (is_array($added)) {
            $message = sprintf('Successfully scanned %s drives.', count($added));

            flash()->success('Success!', $message);

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was an issue scanning this computers disks. Please try again.');

            return redirect()->back();
        }
    }
}
