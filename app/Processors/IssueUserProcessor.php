<?php

namespace App\Processors;

use App\Http\Requests\IssueUserRequest;
use App\Models\Issue;
use App\Models\User;

class IssueUserProcessor extends Processor
{
    /**
     * Constructor.
     *
     * @param Issue $issue
     * @param User  $user
     */
    public function __construct(Issue $issue, User $user)
    {
        $this->issue = $issue;
        $this->user = $user;
    }

    /**
     * Attaches users to the specified issue.
     *
     * @param IssueUserRequest $request
     * @param int|string       $id
     *
     * @return bool
     */
    public function store(IssueUserRequest $request, $id)
    {
        $issue = $this->issue->findOrFail($id);

        $this->authorize('addUsers', $issue);

        if ($request->has('users')) {
            $users = $this->user->find($request->input('users'));

            $issue->users()->sync($users);

            return true;
        }

        $issue->users()->detach();

        return true;
    }
}
