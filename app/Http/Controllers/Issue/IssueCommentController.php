<?php

namespace App\Http\Controllers\Issue;

use App\Http\Controllers\Controller;
use App\Http\Requests\IssueCommentRequest;
use App\Processors\IssueCommentProcessor;

class IssueCommentController extends Controller
{
    /**
     * @var IssueCommentProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param IssueCommentProcessor $processor
     */
    public function __construct(IssueCommentProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Creates a comment and attaches it to the specified issue.
     *
     * @param IssueCommentRequest $request
     * @param int|string          $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(IssueCommentRequest $request, $id)
    {
        if ($this->processor->store($request, $id)) {
            flash()->success('Success!', 'Successfully added comment.');

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was a problem adding a comment. Please try again.');

            return redirect()->back();
        }
    }

    /**
     * Displays the form to edit the specified issue comment.
     *
     * @param int|string $id
     * @param int|string $commentid
     *
     * @return \Illuminate\View\View
     */
    public function edit($id, $commentid)
    {
        return $this->processor->edit($id, $commentid);
    }

    /**
     * Updates the specified issue comment.
     *
     * @param IssueCommentRequest $request
     * @param int|string          $id
     * @param int|string          $commentId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(IssueCommentRequest $request, $id, $commentId)
    {
        if ($this->processor->update($request, $id, $commentId)) {
            flash()->success('Success!', 'Successfully updated comment.');

            return redirect()->route('issues.show', [$id]);
        } else {
            flash()->error('Error!', 'There was a problem updating this comment. Please try again.');

            return redirect()->route('issues.show', [$id]);
        }
    }

    /**
     * Deletes the specified issues comment.
     *
     * @param int|string $id
     * @param int|string $commentId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, $commentId)
    {
        if ($this->processor->destroy($id, $commentId)) {
            flash()->success('Success!', 'Successfully deleted comment.');

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was a problem deleting this comment. Please try again.');

            return redirect()->back();
        }
    }
}
