<?php

namespace Lorisleiva\LaravelDeployer\Test;

use Illuminate\Support\Facades\Artisan;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Lorisleiva\LaravelDeployer\LaravelDeployerServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(__DIR__ . '/dummyApp');
    }

    public function artisan($command, $parameters = [])
    {
        Artisan::call($command, $parameters);
        return Artisan::output();
    }
}