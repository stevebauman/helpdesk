<?php

namespace App\Http\Presenters\PasswordFolder;

use App\Http\Presenters\Presenter;
use App\Models\Password;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;

class PasswordPresenter extends Presenter
{
    /**
     * Returns a table of all of the users passwords.
     *
     * @param \Illuminate\Database\Eloquent\Builder|Password $password
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table($password)
    {
        return $this->table->of('passwords', function (TableGrid $table) use ($password) {
            $table->attributes('class', 'table table-hover');

            $table->with($password)->paginate($this->perPage);

            $table->column('title', function ($column) {
                $column->label = 'Title';
                $column->value = function (Password $password) {
                    return link_to_route('passwords.show', $password->title, [$password->getKey()]);
                };
            });

            $table->column('website', function ($column) {
                $column->label = 'Website';
            });

            $table->column('created_at', function ($column) {
                $column->label = 'Created';
            });
        });
    }

    /**
     * Returns a new form of the specified password.
     *
     * @param Password  $password
     * @param bool|true $viewing
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Password $password, $viewing = false)
    {
        return $this->form->of('passwords', function (FormGrid $form) use ($password, $viewing) {
            if ($password->exists) {
                if ($viewing) {
                    $form->setup($this, null, $password);
                } else {
                    $form->setup($this, route('passwords.update', $password->getKey()), $password, [
                        'method' => 'PATCH',
                    ]);
                }

                $form->submit = 'Save';
            } else {
                $form->setup($this, route('passwords.store', $password->getKey()), $password, [
                    'method' => 'POST',
                ]);

                $form->submit = 'Create';
            }

            $form->fieldset(function (Fieldset $fieldset) use ($viewing) {
                $fieldset->control('input:text', 'title')
                    ->label('Title')
                    ->attributes([
                        'placeholder' => 'Title of the Password',
                        ($viewing ? 'disabled' : null),
                    ]);

                $fieldset->control('input:text', 'website')
                    ->label('Website')
                    ->attributes([
                        'placeholder' => 'Website',
                        ($viewing ? 'disabled' : null),
                    ]);

                $fieldset->control('input:text', 'username')
                    ->label('Username')
                    ->attributes([
                        'placeholder'  => 'The Username for the password',
                        'autocomplete' => 'new-username',
                        ($viewing ? 'disabled' : null),
                    ]);

                $fieldset->control('input:password', 'password')
                    ->label('Password')
                    ->attributes([
                        'class'        => 'password-show',
                        'placeholder'  => 'Enter your Password',
                        'autocomplete' => 'new-password',
                    ]);

                $fieldset->control('input:textarea', 'notes')
                    ->label('Notes')
                    ->attributes([
                        'placeholder' => 'Notes',
                        ($viewing ? 'disabled' : null),
                    ]);
            });
        });
    }

    /**
     * Returns a new navbar for the users password index.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar()
    {
        return $this->fluent([
            'id'         => 'passwords',
            'title'      => 'Passwords',
            'url'        => route('passwords.index'),
            'menu'       => view('pages.passwords._nav'),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }
}
