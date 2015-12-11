<?php

namespace App\Http\Presenters;

use App\Models\Label;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;
use Orchestra\Support\Facades\HTML;

class LabelPresenter extends Presenter
{
    /**
     * Returns a table of all labels.
     *
     * @param Label $label
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(Label $label)
    {
        return $this->table->of('labels', function (TableGrid $table) use ($label) {
            $table->with($label)->paginate($this->perPage);

            $table->sortable([
                'name',
            ]);

            $table->attributes([
                'class' => 'table table-hover',
            ]);

            $table->column('name', function ($column) {
                $column->label = 'Label';

                $column->value = function (Label $label) {
                    return link_to_route('labels.edit', $label->getDisplayLarge(), [$label->getKey()]);
                };
            });
        });
    }

    /**
     * Returns a form for labels.
     *
     * @param Label $label
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Label $label)
    {
        return $this->form->of('label', function (FormGrid $form) use ($label) {
            if ($label->exists) {
                $form->setup($this, route('labels.update', [$label->getKey()]), $label, [
                    'method' => 'PATCH',
                ]);

                $form->submit = 'Save';
            } else {
                $form->setup($this, route('labels.store'), $label, [
                    'method' => 'POST',
                ]);

                $form->submit = 'Create';
            }

            $colors = $label::getColors();

            $options = [];

            foreach ($colors as $color) {
                $options[$color] = HTML::create('span', ucfirst($color), ['class' => "label label-$color"]);
            }

            $form->fieldset(function (Fieldset $fieldset) use ($options) {
                $fieldset->control('input:text', 'name')
                    ->label('Name')
                    ->attributes(['placeholder' => 'Name']);

                $fieldset->control('select', 'color')
                    ->label('Color')
                    ->options($options)
                    ->value(function (Label $label) {
                        return $label->color;
                    })
                    ->attributes([
                        'class'       => 'select-label-color',
                        'placeholder' => 'Select a color',
                    ]);
            });
        });
    }

    /**
     * Returns a new navbar for the label index.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar()
    {
        return $this->fluent([
            'id'         => 'labels',
            'title'      => 'Labels',
            'url'        => route('labels.index'),
            'menu'       => view('pages.labels._nav'),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }
}
