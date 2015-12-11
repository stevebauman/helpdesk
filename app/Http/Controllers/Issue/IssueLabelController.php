<?php

namespace App\Http\Controllers\Issue;

use App\Http\Controllers\Controller;
use App\Http\Requests\IssueLabelRequest;
use App\Processors\IssueLabelProcessor;

class IssueLabelController extends Controller
{
    /**
     * @var IssueLabelProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param IssueLabelProcessor $processor
     */
    public function __construct(IssueLabelProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Updates the specified issue labels.
     *
     * @param IssueLabelRequest $request
     * @param int|string        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(IssueLabelRequest $request, $id)
    {
        if ($this->processor->store($request, $id)) {
            flash()->success('Success!', 'Successfully updated labels for this issue.');

            return redirect()->route('issues.show', [$id]);
        } else {
            flash()->error('Error!', 'There was an issue adding labels to this issue. Please try again.');

            return redirect()->route('issues.show', [$id]);
        }
    }
}
