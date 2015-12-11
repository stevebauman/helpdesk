<?php

namespace App\Http\Presenters\Device;

use App\Http\Presenters\Presenter;
use App\Models\Computer;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;

class ComputerAccessPresenter extends Presenter
{
    /**
     * Returns a new form of the specified computers access.
     *
     * @param Computer $computer
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Computer $computer)
    {
        return $this->form->of('computers.access', function (FormGrid $form) use ($computer) {
            $form->setup($this, route('devices.computers.access.update', [$computer->getKey()]), $computer);

            $form->submit = 'Save';

            $form->fieldset(function (Fieldset $fieldset) use ($computer) {
                if ($computer->access) {
                    $ad = $computer->access->active_directory;
                    $wmi = $computer->access->wmi;
                    $wmiUsername = $computer->access->wmi_username;
                } else {
                    $ad = false;
                    $wmi = false;
                    $wmiUsername = null;
                }

                $fieldset->control('input:checkbox', 'Exists in Active Directory?')
                    ->attributes([
                        'class'     => 'switch-mark',
                        ($ad ? 'checked' : null),
                    ])
                    ->name('active_directory')
                    ->value(1);

                $fieldset->control('input:checkbox', 'Can be accessed through WMI?')
                    ->attributes([
                        'class'     => 'switch-mark',
                        ($wmi ? 'checked' : null),
                    ])
                    ->name('wmi')
                    ->value(1);

                // Only if Active Directory connection is available,
                // we'll show the same credentials as AD checkbox
                if ($ad) {
                    $fieldset->control('input:checkbox', 'Same credentials as AD?')
                        ->attributes([
                            'class' => 'switch-mark',
                            ($wmiUsername ? null : 'checked'),
                        ])
                        ->name('wmi_credentials')
                        ->value(1);
                }

                $fieldset->control('input:text', 'WMI Username')
                    ->label('WMI Username')
                    ->attributes([
                        'autocomplete' => 'new-username',
                        'placeholder'  => 'The WMI Username',
                    ])
                    ->value($wmiUsername)
                    ->name('wmi_username');

                $fieldset->control('input:password', 'WMI Password')
                    ->label('WMI Password')
                    ->attributes([
                        'autocomplete' => 'new-password',
                        'placeholder'  => 'The WMI Password',
                    ])
                    ->name('wmi_password');
            });
        });
    }
}
