<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy extends Policy
{
    /**
     * {@inheritdoc}
     */
    public $actions = [
        'Create Comment',
        'Edit Comment',
        'Update Comment',
        'Delete Comment',
    ];

    /**
     * Returns true / false if the specified user
     * can edit the specified comment.
     *
     * @return bool
     */
    public function store()
    {
        return $this->can('create-comment');
    }

    /**
     * Returns true / false if the specified user
     * can edit the specified comment.
     *
     * @param User    $user
     * @param Comment $comment
     *
     * @return bool
     */
    public function edit(User $user, Comment $comment)
    {
        return $this->can('edit-comment') && $user->getKey() === $comment->user_id;
    }

    /**
     * Returns true / false if the specified user
     * can update the specified comment.
     *
     * @param User    $user
     * @param Comment $comment
     *
     * @return bool
     */
    public function update(User $user, Comment $comment)
    {
        return $user->getKey() === $comment->user_id;
    }

    /**
     * Returns true / false if the specified user
     * can delete the specified comment.
     *
     * @param User    $user
     * @param Comment $comment
     *
     * @return bool
     */
    public function destroy(User $user, Comment $comment)
    {
        return $user->getKey() === $comment->user_id;
    }
}
