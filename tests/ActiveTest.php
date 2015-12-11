<?php

namespace App\Tests;

use App\Http\Active;
use Illuminate\Support\Facades\Route;

class ActiveTest extends TestCase
{
    protected function newActive()
    {
        return new Active();
    }

    public function test_with_current_route()
    {
        Route::shouldReceive('currentRouteName')->once()->andReturn('issues.index');

        $output = $this->newActive()->route('issues.index');

        $this->assertEquals('active', $output);
    }

    public function test_with_wildcard_current_route()
    {
        Route::shouldReceive('currentRouteName')->once()->andReturn('issues.index');

        $output = $this->newActive()->route('issues.*');

        $this->assertEquals('active', $output);
    }

    public function test_with_multiple_routes()
    {
        Route::shouldReceive('currentRouteName')->once()->andReturn('issues.index');

        $routes = [
            'some.route',
            'issues.index',
            'some.other.route',
        ];

        $output = $this->newActive()->routes($routes);

        $this->assertEquals('active', $output);
    }

    public function test_with_multiple_routes_wildcard()
    {
        Route::shouldReceive('currentRouteName')->once()->andReturn('issues.index');

        $routes = [
            'some.route',
            'issues.*',
            'some.other.route',
        ];

        $output = $this->newActive()->routes($routes);

        $this->assertEquals('active', $output);
    }

    public function test_with_non_current_route()
    {
        Route::shouldReceive('currentRouteName')->once()->andReturn('none');

        $output = $this->newActive()->route('issues.index');

        $this->assertNull($output);
    }

    public function test_with_non_current_routes_wildcard()
    {
        Route::shouldReceive('currentRouteName')->once()->andReturn('none');

        $routes = [
            'some.route.*',
            'some.*',
        ];

        $output = $this->newActive()->routes($routes);

        $this->assertNull($output);
    }

    public function test_with_custom_output()
    {
        Route::shouldReceive('currentRouteName')->once()->andReturn('issues.index');

        $output = $this->newActive()->output('custom')->route('issues.index');

        $this->assertEquals('custom', $output);
    }

    public function test_with_only_wildcard()
    {
        Route::shouldReceive('currentRouteName')->once()->andReturn('issues.index');

        $output = $this->newActive()->route('*');

        $this->assertNull($output);
    }
}
