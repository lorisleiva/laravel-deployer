<?php

namespace Lorisleiva\LaravelDeployer\Test;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Lorisleiva\LaravelDeployer\LaravelDeployerServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(__DIR__ . '/fixtures/repository');
    }

    public function artisan($command, $parameters = [])
    {
        Artisan::call($command, $parameters + ['--no-ansi' => true]);
        return Artisan::output();
    }

    public function runInRepository($command)
    {
        $this->exec("cd " . static::REPOSITORY . " && $command");
    }

    public function runInRoot($command)
    {
        $this->exec("cd " . static::TMP . " && $command");
    }

    public function exec($command)
    {
        $process = new Process($command);
        $process->mustRun();
        return trim($process->getOutput());
    }
}