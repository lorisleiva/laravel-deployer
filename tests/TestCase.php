<?php

namespace Lorisleiva\LaravelDeployer\Test;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class TestCase extends \Orchestra\Testbench\TestCase
{
    const BASE_REPOSITORY = __DIR__ . '/fixtures/repository';
    const CONFIGS = __DIR__ . '/fixtures/configs';

    protected function getPackageProviders($app)
    {
        return ['Lorisleiva\LaravelDeployer\LaravelDeployerServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(static::BASE_REPOSITORY);
    }

    public function artisan($command, $parameters = [])
    {
        Artisan::call($command, $parameters + ['--no-ansi' => true]);
        return Artisan::output();
    }

    public function exec($command)
    {
        $process = new Process($command);
        $process->mustRun();
        return trim($process->getOutput());
    }
}
