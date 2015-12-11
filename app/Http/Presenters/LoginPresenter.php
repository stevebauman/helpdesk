<?php

namespace App\Http\Presenters;

use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;

class LoginPresenter extends Presenter
{
    /**
     * Returns a form for the login page.
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form()
    {
        return $this->form->of('label', function (FormGrid $form) {
            $form->attributes(['url' => route('auth.login.perform')]);

            $form->layout('components.form-login');

            $form->submit = 'Sign In';

            $form->fieldset(function (Fieldset $fieldset) {
                $fieldset->control('input:text', 'email')
                    ->label('Email')
                    ->attributes(['placeholder' => 'Enter your Email']);

                $fieldset->control('input:password', 'password')
                    ->label('Password')
                    ->attributes(['placeholder' => 'Enter your Password']);
            });
        });
    }
}
