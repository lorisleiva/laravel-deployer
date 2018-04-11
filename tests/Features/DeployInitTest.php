<?php

namespace Lorisleiva\LaravelDeployer\Test\Commands;

use Illuminate\Contracts\Console\Kernel;
use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class DeployInitTest extends DeploymentTestCase
{
    /** @test */
    function it_generates_a_minimal_config_file()
    {
        $this->artisan('deploy:init', ['-n']);

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
                    "artisan:storage:link",
                    "artisan:view:clear",
                    "artisan:cache:clear",
                    "artisan:config:cache",
                    "artisan:optimize",
                    "artisan:migrate",
                ],
                "done" => [],
                "fail" => [],
                "success" => [],
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

    /** @test */
    function it_generates_a_full_config_file_with_forge_defaults()
    {
        $this->artisan('deploy:init', [ 
            'hostname' => 'elegon.io', 
            '--all' => true, 
            '--forge' => true,
        ]);

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
                    "artisan:storage:link",
                    "artisan:view:clear",
                    "artisan:cache:clear",
                    "artisan:config:cache",
                    "artisan:optimize",
                    "artisan:migrate",
                    "artisan:horizon:terminate",
                ],
                "done" => [
                    'fpm:reload',
                ],
                "fail" => [],
                "success" => [],
            ],
            "options" => [
                "application" => "Laravel",
                "repository" => "",
                'php_fpm_service' => 'php7.1-fpm',
            ],
            "hosts" => [
                "elegon.io" => [
                    "deploy_path" => "/home/forge/elegon.io",
                    "user" => "forge",
                ],
            ],
            "localhost" => [],
            "include" => [],
            "custom_deployer_file" => false,
        ], $configs);
    }
}