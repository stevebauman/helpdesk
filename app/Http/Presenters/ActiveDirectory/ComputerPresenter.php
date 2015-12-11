<?php

namespace App\Http\Presenters\ActiveDirectory;

use Adldap\Models\Computer as AdComputer;
use App\Http\Presenters\Presenter;
use App\Models\Computer;
use Carbon\Carbon;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;
use Orchestra\Support\Facades\HTML;

class ComputerPresenter extends Presenter
{
    /**
     * Returns a new table of all active directory computers.
     *
     * @param array $computers
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(array $computers = [])
    {
        return $this->table->of('active-directory.computers', function (TableGrid $table) use ($computers) {
            $table->attributes('class', 'table table-hover');

            $table->rows($computers);

            $table->column('name', function ($column) {
                $column->label = 'Name';
                $column->value = function (AdComputer $computer) {
                    return $computer->getName();
                };
            });

            $table->column('created_at', function ($column) {
                $column->value = function (AdComputer $computer) {
                    return Carbon::createFromTimeStamp($computer->getCreatedAtTimestamp())->diffForHumans();
                };
            });

            $table->column('description', function ($column) {
                $column->label = 'Description';
                $column->value = function (AdComputer $computer) {
                    return $computer->getDescription();
                };
            });

            $table->column('operating_system', function ($column) {
                $column->label = 'Operating System';
                $column->value = function (AdComputer $computer) {
                    return $computer->getOperatingSystem();
                };
            });

            $table->column('add', function ($column) {
                $column->attributes(function () {
                    return ['class' => 'text-center'];
                });

                $column->label = 'Import';
                $column->value = function (AdComputer $computer) {
                    $exists = Computer::where('dn', $computer->getDn())->first();

                    if ($exists) {
                        return $this->formAdded();
                    } else {
                        return $this->formAdd($computer);
                    }
                };
            });
        });
    }

    /**
     * Returns a form for adding an AD computer.
     *
     * @param AdComputer $computer
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function formAdd(AdComputer $computer)
    {
        $key = $computer->getDn();

        return $this->form->of($key, function (FormGrid $form) use ($computer) {
            $form->attributes([
                'url'    => route('active-directory.computers.store'),
                'method' => 'POST',
            ]);

            $form->hidden('dn', function ($field) use ($computer) {
                $field->value = $computer->getDn();
            });

            $form->submit = 'Import';
        });
    }

    /**
     * Returns a button displaying that the computer has already been added.
     *
     * @return string
     */
    public function formAdded()
    {
        return HTML::create('input', null, [
            'type'     => 'submit',
            'class'    => 'btn btn-primary',
            'value'    => 'Imported',
            'disabled' => true,
        ]);
    }

    /**
     * Returns a new navbar for the active directory computers index.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar()
    {
        return $this->fluent([
            'id'         => 'ad-computers',
            'title'      => 'Computers',
            'url'        => route('active-directory.computers.index'),
            'menu'       => view('pages.active-directory.computers._nav'),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }
}
