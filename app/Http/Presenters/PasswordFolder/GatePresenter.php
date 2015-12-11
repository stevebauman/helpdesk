<?php

namespace App\Http\Presenters\PasswordFolder;

use App\Http\Presenters\Presenter;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;

class GatePresenter extends Presenter
{
    /**
     * Returns a new PIN form for the password folder gate.
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form()
    {
        return $this->form->of('passwords.gate', function (FormGrid $form) {
            $form->attributes([
                'url'    => route('passwords.gate.unlock'),
                'method' => 'POST',
            ]);

            $form->layout('pages.passwords._form');

            $form->submit = 'Unlock';

            $form->fieldset(function (Fieldset $fieldset) {
                $fieldset->control('input:password', 'pin')
                    ->label('Pin')
                    ->attributes(['placeholder' => 'Enter your Pin']);
            });
        });
    }
}
