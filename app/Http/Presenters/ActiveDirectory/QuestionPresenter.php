<?php

namespace App\Http\Presenters\ActiveDirectory;

use App\Http\Presenters\Presenter;
use App\Models\Question;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;

class QuestionPresenter extends Presenter
{
    /**
     * Returns a new table of all active
     * directory security questions.
     *
     * @param Question $question
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(Question $question)
    {
        return $this->table->of('active-directory.questions', function (TableGrid $table) use ($question) {
            $table->with($question->query())->paginate($this->perPage);

            $table->searchable(['content']);

            $table->attributes('class', 'table table-hover');

            $table->column('content', function ($column) {
                $column->label = 'Question';

                $column->value = function (Question $question) {
                    return link_to_route('active-directory.questions.edit', $question->content, [$question->getKey()]);
                };
            });
        });
    }

    /**
     * Returns a new form of the specified question.
     *
     * @param Question $question
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Question $question)
    {
        return $this->form->of('active-directory.question', function (FormGrid $form) use ($question) {
            if ($question->exists) {
                $form->setup($this, route('active-directory.questions.update', [$question->getKey()]), $question, [
                    'method' => 'PATCH',
                ]);

                $form->submit = 'Save';
            } else {
                $form->setup($this, route('active-directory.questions.store'), $question, [
                    'method' => 'POST',
                ]);

                $form->submit = 'Create';
            }

            $form->fieldset(function (Fieldset $fieldset) {
                $fieldset->control('input:text', 'content')
                    ->label('Question')
                    ->attributes(['placeholder' => 'Question']);
            });
        });
    }

    /**
     * Returns a new navbar for the questions index.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar()
    {
        return $this->fluent([
            'id'         => 'questions',
            'title'      => 'Questions',
            'url'        => route('active-directory.questions.index'),
            'menu'       => view('pages.active-directory.questions._nav'),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }
}
