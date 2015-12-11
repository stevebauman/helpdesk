<?php

namespace App\Http\Presenters\PasswordFolder;

use App\Http\Presenters\Presenter;
use App\Models\PasswordFolder;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;

class SetupPresenter extends Presenter
{
    /**
     * Returns a new PasswordFolder form.
     *
     * @param PasswordFolder $folder
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form($folder)
    {
        return $this->form->of('passwords.setup', function (FormGrid $form) use ($folder) {
            $form->setup($this, route('passwords.setup.finish'), $folder, [
                'method' => 'POST',
            ]);

            $form->layout('pages.passwords._form');

            $form->submit = 'Setup';

            $form->fieldset(function (Fieldset $fieldset) {
                $fieldset->control('input:password', 'pin')
                    ->label('Pin')
                    ->attributes(['placeholder' => 'Enter your Pin']);

                $fieldset->control('input:password', 'pin_confirmation')
                    ->label('Confirm Pin')
                    ->attributes([
                        'placeholder' => 'Confirm your above Pin',
                    ]);
            });
        });
    }
}
