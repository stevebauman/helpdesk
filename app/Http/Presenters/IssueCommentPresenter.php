<?php

namespace App\Http\Presenters;

use App\Models\Comment;
use App\Models\Issue;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;

class IssueCommentPresenter extends Presenter
{
    /**
     * Returns a form of the issue comment.
     *
     * @param Issue   $issue
     * @param Comment $comment
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Issue $issue, Comment $comment)
    {
        return $this->form->of('issue.comment', function (FormGrid $form) use ($issue, $comment) {
            // Check if the issue already has a resolution
            $hasResolution = $issue->findCommentResolution();

            $attributes = [];

            if ($comment->exists) {
                $hash = sprintf('#comment-%s', $comment->getKey());
                $url = route('issues.comments.update', [$issue->getKey(), $comment->getKey(), $hash]);
                $attributes = ['method' => 'PATCH'];

                $form->submit = 'Save';
            } else {
                $url = route('issues.comments.store', [$issue->getKey(), '#comment']);

                $form->submit = 'Comment';
            }

            $form->setup($this, $url, $comment, $attributes);

            // Setup the form fieldset
            $form->fieldset(function (Fieldset $fieldset) use ($comment, $hasResolution) {
                $fieldset->control('input:textarea', 'content')
                    ->label('Comment')
                    ->attributes([
                        'placeholder'  => 'Leave a comment',
                        'data-provide' => 'markdown',
                        'id'           => 'comment',
                    ]);

                $isResolution = $comment->isResolution();

                // If the issue doesn't have a resolution, or the current comment
                // is the resolution, we'll add the mark resolution checkbox
                if (!$hasResolution || $isResolution) {
                    $fieldset->control('input:checkbox', 'Mark as Resolution')
                        ->attributes([
                            'class' => 'switch-mark',
                            ($isResolution ? 'checked' : null),
                        ])
                        ->name('resolution')
                        ->value(1);
                }
            });
        });
    }
}
