<?php

namespace App\Http\Presenters\Device;

use App\Http\Presenters\Presenter;
use App\Models\Computer;
use App\Models\ComputerType;
use App\Models\OperatingSystem;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;

class ComputerPresenter extends Presenter
{
    /**
     * Returns a new table of all computers.
     *
     * @param Computer $computer
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(Computer $computer)
    {
        return $this->table->of('computers', function (TableGrid $table) use ($computer) {
            $table->with($computer)->paginate($this->perPage);

            $table->attributes('class', 'table table-hover');

            $table->sortable([
                'name',
                'description',
            ]);

            $table->searchable([
                'name',
                'description',
            ]);

            $table->column('name', function ($column) {
                $column->label = 'Name';
                $column->value = function (Computer $computer) {
                    return link_to_route('devices.computers.show', $computer->name, [$computer->getKey()]);
                };
            });

            $table->column('access', function ($column) {
                $column->label = 'Access';
                $column->value = function (Computer $computer) {
                    return $computer->getAccessChecks();
                };
            });

            $table->column('description', function ($column) {
                $column->label = 'Description';
                $column->value = function (Computer $computer) {
                    return $computer->description;
                };
            });

            $table->column('os', function ($column) {
                $column->label = 'Operating System';
                $column->value = function (Computer $computer) {
                    return $computer->getCompleteOs();
                };
            });
        });
    }

    /**
     * Returns a new form for computers.
     *
     * @param Computer $computer
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Computer $computer)
    {
        return $this->form->of('computers', function (FormGrid $form) use ($computer) {
            $operatingSystems = OperatingSystem::lists('name', 'id');
            $types = ComputerType::lists('name', 'id');

            if ($computer->exists) {
                $form->setup($this, route('devices.computers.update', [$computer->getKey()]), $computer, [
                    'method' => 'PATCH',
                ]);

                $form->submit = 'Save';
            } else {
                $form->setup($this, route('devices.computers.store'), $computer, [
                    'method' => 'POST',
                ]);

                $form->submit = 'Create';
            }

            $form->fieldset(function (Fieldset $fieldset) use ($computer, $operatingSystems, $types) {
                // The computer name text field
                $fieldset->control('input:text', 'name')
                    ->label('Name')
                    ->attributes(['placeholder' => 'Name']);

                // The computer OS select field
                $fieldset->control('select', 'os')
                    ->label('Operating System')
                    ->options($operatingSystems)
                    ->value(function (Computer $computer) {
                        if ($computer->os instanceof OperatingSystem) {
                            return $computer->os->getKey();
                        }
                    })
                    ->attributes([
                        'class'       => 'form-control',
                        'placeholder' => 'Select An Operating System',
                    ]);

                // The computer type select field
                $fieldset->control('select', 'type')
                    ->label('Type')
                    ->options($types)
                    ->value(function (Computer $computer) {
                        if ($computer->type instanceof ComputerType) {
                            return $computer->type->getKey();
                        }
                    })
                    ->attributes([
                        'class'       => 'form-control',
                        'placeholder' => 'Select a Type',
                    ]);

                // The computer model text field
                $fieldset->control('input:text', 'model')
                    ->label('Model')
                    ->attributes(['placeholder' => 'Model']);

                // The computer description text field
                $fieldset->control('input:textarea', 'description')
                    ->label('Description')
                    ->attributes([
                        'placeholder' => 'Description',
                    ]);

                if (!$computer->exists) {
                    // We'll only allow the exists in active directory checkbox if
                    // the computer hasn't been created yet. This is due to
                    // the access panel and can be updated there.
                    $fieldset->control('input:checkbox', 'Exists in Active Directory?')
                        ->attributes([
                            'class' => 'switch-mark',
                        ])
                        ->name('active_directory')
                        ->value(1);
                }
            });
        });
    }

    /**
     * Returns a new navbar for the computer index.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar()
    {
        return $this->fluent([
            'id'         => 'computers',
            'title'      => 'Computers',
            'url'        => route('devices.computers.index'),
            'menu'       => view('pages.devices.computers._nav'),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }
}
