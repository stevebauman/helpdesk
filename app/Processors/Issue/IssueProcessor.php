<?php

namespace App\Processors\Issue;

use App\Http\Presenters\Issue\IssuePresenter;
use App\Http\Requests\IssueRequest;
use App\Jobs\Issue\Close;
use App\Jobs\Issue\Open;
use App\Jobs\Issue\Store;
use App\Jobs\Issue\Update;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\Label;
use App\Processors\Processor;

class IssueProcessor extends Processor
{
    /**
     * @var Issue
     */
    protected $issue;

    /**
     * @var Label
     */
    protected $label;

    /**
     * @var IssuePresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param Issue          $issue
     * @param Label          $label
     * @param IssuePresenter $presenter
     */
    public function __construct(Issue $issue, Label $label, IssuePresenter $presenter)
    {
        $this->issue = $issue;
        $this->label = $label;
        $this->presenter = $presenter;
    }

    /**
     * Returns a table displaying all open issues.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $issues = $this->presenter->tableOpen($this->issue);

        $labels = $this->label->all();

        $navbar = $this->presenter->navbar($labels);

        return view('pages.issues.index', compact('issues', 'navbar'));
    }

    /**
     * Returns a table displaying all closed issues.
     *
     * @return \Illuminate\View\View
     */
    public function closed()
    {
        $issues = $this->presenter->tableClosed($this->issue);

        $labels = $this->label->all();

        $navbar = $this->presenter->navbar($labels);

        return view('pages.issues.index', compact('issues', 'navbar'));
    }

    /**
     * Displays the form for creating a new issue.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $form = $this->presenter->form($this->issue);

        return view('pages.issues.create', compact('form'));
    }

    /**
     * Creates a new issue.
     *
     * @param IssueRequest $request
     *
     * @return bool|Issue
     */
    public function store(IssueRequest $request)
    {
        $issue = $this->issue->newInstance();

        return $this->dispatch(new Store($request, $issue));
    }

    /**
     * Returns the issue with the specified ID.
     *
     * @param int|string $id
     *
     * @return Issue
     */
    public function show($id)
    {
        $with = ['comments', 'labels'];

        // Find the issue.
        $issue = $this->issue->with($with)->findOrFail($id);

        // Check user authorization.
        $this->authorize($issue);

        // Retrieve the issue resolution if there is one.
        $resolution = $issue->comments->first(function ($key, Comment $comment) {
            return $comment->resolution;
        });

        $formComment = $this->presenter->formComment($issue);

        $formLabels = $this->presenter->formLabels($issue);

        $formUsers = $this->presenter->formUsers($issue);

        return view('pages.issues.show', compact('issue', 'resolution', 'formComment', 'formLabels', 'formUsers'));
    }

    /**
     * Displays the form for editing an issue.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Find the issue.
        $issue = $this->issue->findOrFail($id);

        // Check user authorization.
        $this->authorize($issue);

        $form = $this->presenter->form($issue);

        return view('pages.issues.edit', compact('form'));
    }

    /**
     * Updates the specified issue.
     *
     * @param IssueRequest $request
     * @param int|string   $id
     *
     * @return Issue|false
     */
    public function update(IssueRequest $request, $id)
    {
        // Find the issue.
        $issue = $this->issue->findOrFail($id);

        // Check user authorization.
        $this->authorize($issue);

        return $this->dispatch(new Update($request, $issue));
    }

    /**
     * Deletes the specified issue.
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function destroy($id)
    {
        // Find the issue.
        $issue = $this->issue->findOrFail($id);

        // Check user authorization.
        $this->authorize($issue);

        return $issue->delete();
    }

    /**
     * Closes an issue.
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function close($id)
    {
        // Find the issue.
        $issue = $this->issue->findOrFail($id);

        // Check user authorization.
        $this->authorize($issue);

        return $this->dispatch(new Close($issue));
    }

    /**
     * Re-Opens an issue.
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function open($id)
    {
        // Find the issue.
        $issue = $this->issue->findOrFail($id);

        // Check user authorization.
        $this->authorize($issue);

        return $this->dispatch(new Open($issue));
    }
}
