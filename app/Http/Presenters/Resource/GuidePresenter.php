<?php

namespace App\Http\Presenters\Resource;

use App\Http\Presenters\Presenter;
use App\Models\Guide;
use App\Models\GuideStep;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;

class GuidePresenter extends Presenter
{
    /**
     * Returns a new table of all guides.
     *
     * @param Guide $guide
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(Guide $guide)
    {
        $guide = $guide->query()->latest();

        return $this->table->of('resources.guides', function (TableGrid $table) use ($guide) {
            $table->with($guide)->paginate($this->perPage);

            $table->searchable([
                'title',
                'description',
            ]);

            $table->attributes(['class' => 'table table-hover']);

            $table
                ->column('title')
                ->value(function (Guide $guide) {
                    return link_to_route('resources.guides.show', $guide->title, [$guide->getSlug()]);
                });

            $table
                ->column('summary')
                ->value(function (Guide $guide) {
                    return $guide->summary();
                });

            $table
                ->column('published')
                ->value(function (Guide $guide) {
                    return $guide->publishedLabel();
                });

            $table
                ->column('created_at')
                ->label('Created')
                ->value(function (Guide $guide) {
                    return $guide->createdAtHuman();
                });
        });
    }

    /**
     * Returns a new form of the specified guide.
     *
     * @param Guide $guide
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(Guide $guide)
    {
        return $this->form->of('resources.guides', function (FormGrid $form) use ($guide) {
            if ($guide->exists) {
                $route = route('resources.guides.update', [$guide->getSlug()]);
                $method = 'PATCH';

                $form->submit = 'Save';
            } else {
                $route = route('resources.guides.store');
                $method = 'POST';

                $form->submit = 'Create';
            }

            $form->setup($this, $route, $guide, compact('method'));

            $form->fieldset(function (Fieldset $fieldset) use ($guide) {
                $fieldset
                    ->control('input:text', 'title')
                    ->attributes([
                        'class'           => 'slug',
                        'placeholder'     => 'Enter the guide title',
                        'data-slug-field' => '#slug',
                    ])->value(function (Guide $guide) {
                        return $guide->exists ? $guide->title : 'How To:';
                    });

                $fieldset
                    ->control('input:text', 'slug')
                    ->attributes([
                        'placeholder' => 'Enter the guide slug',
                    ])->value(function (Guide $guide) {
                        return $guide->exists ? $guide->getSlug() : 'how-to';
                    });

                $fieldset
                    ->control('input:textarea', 'description')
                    ->attributes([
                        'placeholder'  => 'Enter the guide description',
                        'data-provide' => 'markdown',
                    ]);

                $fieldset
                    ->control('input:checkbox', 'publish')
                    ->attributes([
                        'class' => 'switch-mark',
                        ($guide->published ? 'checked' : null),
                    ])
                    ->value(1);
            });
        });
    }

    /**
     * Returns a new form of the guide step.
     *
     * @param Guide $guide
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function formStep(Guide $guide)
    {
        $presenter = new GuideStepPresenter($this->form, $this->table);

        return $presenter->form($guide, new GuideStep());
    }

    /**
     * Returns a new navbar for the guide index.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar()
    {
        return $this->fluent([
            'id'         => 'guides',
            'title'      => 'Guides',
            'url'        => route('resources.guides.index'),
            'menu'       => view('pages.resources.guides._nav'),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }

    /**
     * Returns a new navbar for the guide step show page.
     *
     * @param Guide $guide
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbarShow(Guide $guide)
    {
        return $this->fluent([
            'id'         => 'guide-steps-show',
            'title'      => 'Actions',
            'url'        => route('resources.guides.show', [$guide->getSlug()]),
            'menu'       => view('pages.resources.guides._nav-show', compact('guide')),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }
}
