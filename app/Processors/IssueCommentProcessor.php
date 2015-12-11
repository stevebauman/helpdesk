<?php

namespace App\Processors;

use App\Http\Presenters\IssueCommentPresenter;
use App\Http\Requests\IssueCommentRequest;
use App\Models\Issue;

class IssueCommentProcessor extends Processor
{
    /**
     * @var Issue
     */
    protected $issue;

    /**
     * @var IssueCommentPresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param Issue                 $issue
     * @param IssueCommentPresenter $presenter
     */
    public function __construct(Issue $issue, IssueCommentPresenter $presenter)
    {
        $this->issue = $issue;
        $this->presenter = $presenter;
    }

    /**
     * Adds a comment to an issue.
     *
     * @param IssueCommentRequest $request
     * @param int|string          $id
     *
     * @return bool|Issue
     */
    public function store(IssueCommentRequest $request, $id)
    {
        $issue = $this->issue->findOrFail($id);

        $this->authorize($issue->comments()->getRelated());

        return $issue->createComment($request->input('content'), $request->input('resolution', false));
    }

    /**
     * Displays the form for editing a comment.
     *
     * @param int|string $id
     * @param int|string $commentId
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return \Illuminate\View\View
     */
    public function edit($id, $commentId)
    {
        $issue = $this->issue->findOrFail($id);

        $comment = $issue->comments()->findOrFail($commentId);

        $this->authorize($comment);

        $form = $this->presenter->form($issue, $comment);

        return view('pages.issues.comments.edit', compact('form'));
    }

    /**
     * Updates an issue comment.
     *
     * @param IssueCommentRequest $request
     * @param int|string          $id
     * @param int|string          $commentId
     *
     * @return bool
     */
    public function update(IssueCommentRequest $request, $id, $commentId)
    {
        $issue = $this->issue->findOrFail($id);

        $content = $request->input('content');
        $resolution = $request->input('resolution', false);

        return $issue->updateComment($commentId, $content, $resolution);
    }

    /**
     * Deletes an issue comment.
     *
     * @param int|string $id
     * @param int|string $commentId
     *
     * @return bool
     */
    public function destroy($id, $commentId)
    {
        $issue = $this->issue->findOrFail($id);

        $comment = $issue->comments()->findOrFail($commentId);

        $this->authorize($comment);

        $issue->comments()->detach($comment);

        return $comment->delete();
    }
}
