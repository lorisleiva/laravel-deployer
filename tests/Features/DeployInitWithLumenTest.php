<?php

namespace Lorisleiva\LaravelDeployer\Test\Features;

use Illuminate\Contracts\Console\Kernel;
use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class DeployInitWithLumenTest extends DeploymentTestCase
{
    protected $configs = null;

    /** @test */
    function it_generates_a_config_file_without_artisan_tasks_not_supported_by_lumen()
    {
        $this->artisan('deploy:init', [ '-n' ]);

        $configs = include static::REPOSITORY . '/config/deploy.php';

        $this->assertEquals([
            "default" => "basic",
            "strategies" => [],
            "hooks" => [
                "start" => [],
                "build" => [
                    "npm:install",
                    "npm:production",
                ],
                "ready" => [
                    "artisan:migrate",
                ],
                "done" => [],
                "fail" => [],
                "success" => [],
                "rollback" => [],
            ],
            "options" => [
                "application" => "Laravel",
                "repository" => "",
            ],
            "hosts" => [
                "example.com" => [
                    "deploy_path" => "/var/www/example.com",
                    "user" => "root",
                ],
            ],
            "localhost" => [],
            "include" => [],
            "custom_deployer_file" => false,
        ], $configs);
    }

    protected function resolveApplication()
    {
        return tap(new LumenApplication($this->getBasePath()), function ($app) {
            $app->bind(
                'Illuminate\Foundation\Bootstrap\LoadConfiguration',
                'Orchestra\Testbench\Bootstrap\LoadConfiguration'
            );
        });
    }
}

class LumenApplication extends \Illuminate\Foundation\Application
{
    const VERSION = 'Lumen (5.6.2)';
}
