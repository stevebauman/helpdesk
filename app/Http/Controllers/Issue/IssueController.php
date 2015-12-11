<?php

namespace App\Http\Controllers\Issue;

use App\Http\Controllers\Controller;
use App\Http\Requests\IssueRequest;
use App\Processors\IssueProcessor;

class IssueController extends Controller
{
    /**
     * @var IssueProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param IssueProcessor $processor
     */
    public function __construct(IssueProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays all issues.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * Displays all closed issues.
     *
     * @return \Illuminate\View\View
     */
    public function closed()
    {
        return $this->processor->closed();
    }

    /**
     * Displays the form for creating an issue.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->processor->create();
    }

    /**
     * Processes creating an issue.
     *
     * @param IssueRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(IssueRequest $request)
    {
        if ($this->processor->store($request)) {
            flash()->success('Success!', 'Successfully created issue.');

            return redirect()->route('issues.index');
        } else {
            flash()->error('Error!', 'There was a problem creating an issue. Please try again.');

            return redirect()->route('issues.create');
        }
    }

    /**
     * Displays the issue.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->processor->show($id);
    }

    /**
     * Displays the form for editing the specified issue.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->processor->edit($id);
    }

    /**
     * Updates the specified issue.
     *
     * @param IssueRequest $request
     * @param int|string   $id
     *
     * @return bool
     */
    public function update(IssueRequest $request, $id)
    {
        if ($this->processor->update($request, $id)) {
            flash()->success('Success!', 'Successfully updated issue.');

            return redirect()->route('issues.show', [$id]);
        } else {
            flash()->error('Error!', 'There was a problem updating this issue. Please try again.');

            return redirect()->route('issues.edit', [$id]);
        }
    }

    /**
     * Deletes the specified issue.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($this->processor->destroy($id)) {
            flash()->success('Success!', 'Successfully deleted issue.');

            return redirect()->route('issues.index');
        } else {
            flash()->error('Error!', 'There was a problem deleting this issue. Please try again.');

            return redirect()->route('issues.show', [$id]);
        }
    }

    /**
     * Closes an issue.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close($id)
    {
        if ($this->processor->close($id)) {
            flash()->success('Success!', 'Successfully closed issue.');

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was a problem closing this issue. Please try again.');

            return redirect()->back();
        }
    }

    /**
     * Re-Opens an issue.
     *
     * @param int|string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function open($id)
    {
        if ($this->processor->open($id)) {
            flash()->success('Success!', 'Successfully re-opened issue.');

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was a problem re-opening this issue. Please try again.');

            return redirect()->back();
        }
    }
}
