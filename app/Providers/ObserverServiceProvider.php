<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * The model observers.
     *
     * @var array
     */
    protected $observers = [
        \App\Models\Guide::class            => \App\Models\Observers\GuideObserver::class,
        \App\Models\GuideStep::class        => \App\Models\Observers\GuideStepObserver::class,
        \App\Models\Computer::class         => \App\Models\Observers\ComputerObserver::class,
        \App\Models\Label::class            => \App\Models\Observers\LabelObserver::class,
        \App\Models\Issue::class            => \App\Models\Observers\IssueObserver::class,
        \App\Models\Comment::class          => \App\Models\Observers\CommentObserver::class,
        \App\Models\Upload::class           => \App\Models\Observers\UploadObserver::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->observers as $model => $observer) {
            $model::observe(new $observer());
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
