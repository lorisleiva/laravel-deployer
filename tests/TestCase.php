<?php

namespace Lorisleiva\LaravelDeployer\Test;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class TestCase extends \Orchestra\Testbench\TestCase
{
    const BASE_REPOSITORY = __DIR__ . '/fixtures/repository';
    const CONFIGS = __DIR__ . '/fixtures/configs';
    const GENERATED_DEPLOY_PATH = 'vendor/lorisleiva/laravel-deployer/.build/deploy.php';

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

    public function exec(string $command)
    {
        $process = Process::fromShellCommandline($command);
        $process->mustRun();
        return trim($process->getOutput());
    }

    public function generatedDeployPath()
    {
        return self::BASE_REPOSITORY . '/' . self::GENERATED_DEPLOY_PATH;
    }
}