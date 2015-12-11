<?php

namespace App\Http\Presenters\ActiveDirectory;

use App\Http\Presenters\Presenter;
use App\Models\Question;
use App\Models\User;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Column;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;

class SetupQuestionPresenter extends Presenter
{
    /**
     * Returns a table of all the users security questions.
     *
     * @param User $user
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(User $user)
    {
        $questions = $user->questions->toArray();

        return $this->table->of('active-directory.security-questions', function (TableGrid $table) use ($questions) {
            $table->rows($questions);

            $table->attributes(['class' => 'table table-hover']);

            $table->column('question', function (Column $column) {
                $column->value = function ($question) {
                    return link_to_route('security-questions.edit', $question['content'], [$question['id']]);
                };
            });
        });
    }

    /**
     * Returns a new form for setting up a users security questions.
     *
     * @param User     $user
     * @param Question $question
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(User $user, Question $question)
    {
        $questions = $question->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->getKey());
        }, '<', 1)->get()->lists('content', 'id');

        return $this->form->of('active-directory.security-questions.setup', function (FormGrid $form) use ($questions, $question) {
            if ($question->exists) {
                // If the question exists, we'll add it to the questions
                // collection so it's available for selection.
                $questions[$question->getKey()] = $question->content;

                // We'll also assume the user is editing the security
                // question so we'll set the route path to update.
                $route = route('security-questions.update', [$question->getKey()]);

                $form->submit = 'Save';
            } else {
                $route = route('security-questions.setup.save');

                $form->submit = 'Next';
            }

            $form->attributes(['url' => $route]);

            $form->fieldset(function (Fieldset $fieldset) use ($questions, $question) {
                $fieldset->control('select', 'question')
                    ->label('Question')
                    ->options($questions)
                    ->value(function () use ($question) {
                        return $question->getKey();
                    });

                $fieldset->control('input:password', 'answer')
                    ->attributes([
                        'class'        => 'password-show',
                        'autocomplete' => 'new-answer',
                        'placeholder'  => 'Enter your security question answer.',
                    ]);
            });
        });
    }
}
