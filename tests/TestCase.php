<?php

namespace App\Tests;

use Illuminate\Support\Facades\Session;
use Orchestra\Testing\ApplicationTestCase;

abstract class TestCase extends ApplicationTestCase
{
    /**
     * Base application namespace.
     *
     * @var string
     */
    protected $baseNamespace = 'App';

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Call the application migrations for testing.
     */
    public function setUp()
    {
        parent::setUp();

        // Run auth migrations
        $this->artisan('auth:migrate');

        // Run control migrations
        $this->artisan('extension:migrate');

        // Run application migrations
        $this->artisan('migrate', [
            '--database' => 'sqlite',
            '--realpath' => realpath('resources/database/migrations'),
        ]);

        // Start the session.
        Session::start();
    }

    /**
     * Get base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        return realpath(__DIR__.'/../');
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Create the SQL lite memory database connection.
        $app['config']->set('database.connections', [
            'testing' => [
                'driver'   => 'sqlite',
                'database' => ':memory:',
            ],
        ]);

        // Set the default database.
        $app['config']->set('database.default', 'testing');

        // Set the HTML table configuration.
        $app['config']->set('orchestra/html::table', [
            'empty' => 'There are no records to display.',
            'view'  => 'components.table',
        ]);

        // Set the HTML table configuration.
        $app['config']->set('orchestra/html::form', [
            'view'      => 'components.form',
            'format'    => '<span class="label label-danger">:message</span>',
            'templates' => [
                'input'    => ['class' => 'col-md-12 input-with-feedback'],
                'password' => ['class' => 'col-md-12 input-with-feedback'],
                'select'   => ['class' => 'col-md-12 input-with-feedback'],
                'textarea' => ['class' => 'col-md-12 input-with-feedback'],
            ],
            'submit'    => 'orchestra/foundation::label.submit',
            'presenter' => 'Orchestra\Html\Form\BootstrapThreePresenter',
        ]);

        // $app['router']->disableFilters();
    }
}
