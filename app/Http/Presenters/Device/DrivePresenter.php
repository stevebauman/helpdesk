<?php

namespace App\Http\Presenters\Device;

use Orchestra\Contracts\Html\Form\Field;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Table\Column;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use App\Http\Presenters\Presenter;
use App\Models\Drive;

class DrivePresenter extends Presenter
{
    /**
     * Returns a new table of all drives.
     *
     * @param Drive $drive
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(Drive $drive)
    {
        return $this->table->of('devices.drives', function (TableGrid $table) use ($drive)
        {
            $table->with($drive)->paginate($this->perPage);

            $table->searchable(['name', 'path']);

            $table->attributes('class', 'table table-hover');

            $table->column('name', function (Column $column)
            {
                $column->value = function (Drive $drive) {
                    return link_to_route('devices.drives.show', $drive->name, [$drive->getKey()]);
                };
            });

            $table->column('path');

            $table->column('is_network', function (Column $column)
            {
                $column->label = 'Network Attached?';

                $column->value = function (Drive $drive) {
                    return ($drive->is_network ? 'Yes' : 'No');
                };
            });
        });
    }

    /**
     * Returns a new form for drives.
     *
     * @param Drive $drive
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Drive $drive)
    {
        return $this->form->of('devices.drive', function (FormGrid $form) use ($drive)
        {
            if ($drive->exists) {
                $form->setup($this, route('devices.drives.update', [$drive->getKey()]), $drive);
                $form->submit = 'Save';
            } else {
                $form->setup($this, route('devices.drives.store'), $drive);
                $form->submit = 'Create';
            }

            $form->fieldset(function (Fieldset $fieldset) use ($drive)
            {
                $fieldset->control('input:text', 'name', function (Field $field)
                {
                    $field->attributes = [
                        'placeholder' => 'Name'
                    ];
                });

                $fieldset->control('input:text', 'path', function (Field $field)
                {
                    $field->attributes = [
                        'placeholder' => 'Local: C:\Windows | Network: \\\\data\company'
                    ];
                });

                $fieldset->control('input:checkbox', 'Is Located on Network')
                    ->attributes([
                        'class' => 'switch-mark',
                        ($drive->is_network ? 'checked' : null)
                    ])
                    ->name('is_network')
                    ->value(1);
            });
        });
    }

    /**
     * Returns a new navbar for the drives index.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar()
    {
        return $this->fluent([
            'id'    => 'drives',
            'title' => 'Drives',
            'url'   => route('devices.drives.index'),
            'menu'  => view('pages.devices.drives._nav'),
            'attributes' => [
                'class' => 'navbar-default'
            ],
        ]);
    }
}
