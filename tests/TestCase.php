<?php

namespace Plank\LaravelHush\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Plank\LaravelHush\HushServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--path' => realpath(__DIR__).'/Database/Migrations',
            '--realpath' => true,
        ])->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            HushServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }
}
