<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy extends Policy
{
    /**
     * {@inheritdoc}
     */
    public $actions = [
        'View All Issues',
        'View Issue',
        'Create Issue',
        'Display Issue',
        'Edit Issue',
        'Open Issue',
        'Close Issue',
        'Delete Issue',
        'Add Labels',
        'Add Users',
    ];

    /**
     * Returns true / false if the specified
     * user can view all issues.
     *
     * @return bool
     */
    public function viewAll()
    {
        return $this->can('view-all-issues');
    }

    /**
     * Returns true / false if the current
     * user can view their own issues.
     *
     * @return bool
     */
    public function index()
    {
        return $this->can('view-issue');
    }

    /**
     * Returns true / false if the current user can create issues.
     *
     * @return bool
     */
    public function create()
    {
        return $this->can('create-issue');
    }

    /**
     * Returns true / false if the specified user
     * can view the specified issue.
     *
     * @param User  $user
     * @param Issue $issue
     *
     * @return bool
     */
    public function show(User $user, Issue $issue)
    {
        return $user->can('display-issue') || $user->getKey() === $issue->user_id;
    }

    /**
     * Returns true / false if the specified user
     * can edit the specified issue.
     *
     * @param User  $user
     * @param Issue $issue
     *
     * @return bool
     */
    public function edit(User $user, Issue $issue)
    {
        return $user->can('edit-issue') || $user->getKey() === $issue->user_id;
    }

    /**
     * Returns true / false if the specified user
     * can update the specified issue.
     *
     * @param User  $user
     * @param Issue $issue
     *
     * @return bool
     */
    public function update(User $user, Issue $issue)
    {
        return $user->can('edit-issue') || $user->getKey() === $issue->user_id;
    }

    /**
     * Returns true / false if the specified user
     * can re-open issues.
     *
     * Only administrators can re-open issues.
     *
     * @param User $user
     *
     * @return bool
     */
    public function open(User $user)
    {
        return $user->can('open-issue');
    }

    /**
     * Returns true / false if the specified user
     * can close issues.
     *
     * Only administrators / issue owners can close issues.
     *
     * @param User  $user
     * @param Issue $issue
     *
     * @return bool
     */
    public function close(User $user, Issue $issue)
    {
        return $user->can('close-issue') || $user->getKey() === $issue->user_id;
    }

    /**
     * Returns true / false if the specified user
     * can delete the specified issue.
     *
     * @param User  $user
     * @param Issue $issue
     *
     * @return bool
     */
    public function destroy(User $user, Issue $issue)
    {
        return $user->can('delete-issue') || $user->getKey() === $issue->user_id;
    }

    /**
     * Returns true / false if the specified user
     * can add labels to issues.
     *
     * @param User $user
     *
     * @return bool
     */
    public function addLabels(User $user)
    {
        return $user->can('add-labels');
    }

    /**
     * Returns true / false if the specified user
     * can add users to issues.
     *
     * @param User $user
     *
     * @return bool
     */
    public function addUsers(User $user)
    {
        return $user->can('add-users');
    }
}
