<?php

namespace App\Http\Presenters\PasswordFolder;

use App\Http\Presenters\Presenter;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Html\Form\Grid as FormGrid;

class PinPresenter extends Presenter
{
    /**
     * Returns a new form for changing the users password folder PIN.
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form()
    {
        return $this->form->of('passwords.change-pin', function (FormGrid $form) {
            $form->attributes([
                'url'    => route('passwords.pin.update'),
                'method' => 'POST',
            ]);

            $form->layout('pages.passwords._form');

            $form->submit = 'Change';

            $form->fieldset(function (Fieldset $fieldset) {
                $fieldset->control('input:password', 'pin')
                    ->label('PIN')
                    ->attributes(['placeholder' => 'Enter your Current PIN']);

                $fieldset->control('input:password', 'new_pin')
                    ->label('New PIN')
                    ->attributes([
                        'placeholder' => 'Enter a new PIN',
                    ]);

                $fieldset->control('input:password', 'new_pin_confirmation')
                    ->label('Confirm New PIN')
                    ->attributes([
                        'placeholder' => 'Confirm your above PIN',
                    ]);
            });
        });
    }
}
