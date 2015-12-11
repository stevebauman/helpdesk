<?php

namespace App\Http\Controllers\Issue;

use App\Http\Controllers\Controller;
use App\Http\Requests\IssueUserRequest;
use App\Processors\IssueUserProcessor;

class IssueUserController extends Controller
{
    /**
     * Constructor.
     *
     * @param IssueUserProcessor $processor
     */
    public function __construct(IssueUserProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Updates the specified issue labels.
     *
     * @param IssueUserRequest $request
     * @param int|string       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(IssueUserRequest $request, $id)
    {
        if ($this->processor->store($request, $id)) {
            flash()->success('Success!', 'Successfully updated users for this issue.');

            return redirect()->route('issues.show', [$id]);
        } else {
            flash()->error('Error!', 'There was an issue adding users to this issue. Please try again.');

            return redirect()->route('issues.show', [$id]);
        }
    }
}
