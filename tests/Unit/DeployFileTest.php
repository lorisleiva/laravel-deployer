<?php

namespace Lorisleiva\LaravelDeployer\Test\Unit;

use Lorisleiva\LaravelDeployer\DeployFile;
use Lorisleiva\LaravelDeployer\Test\TestCase;

class DeployFileTest extends TestCase
{
    /** @test */
    function it_adds_laravel_deployer_recipe_by_default()
    {
        $deployFile = (string) new DeployFile();

        $this->assertStringContainsString("namespace Deployer;", $deployFile);
        $this->assertStringContainsString("require 'recipe/laravel-deployer.php';", $deployFile);
    }

    /** @test */
    function it_should_not_contain_more_than_two_consecutive_empty_lines()
    {
        $deployFile = (string) new DeployFile();

        $this->assertFalse((bool) preg_match("/\n{3,}/", $deployFile));
    }

    /** @test */
    function it_adds_the_default_strategy_as_an_option()
    {
        $deployFile = (string) new DeployFile([
            'default' => 'docker'
        ]);

        $this->assertStringContainsString("set('strategy', 'docker');", $deployFile);
    }

    /** @test */
    function it_use_the_basic_strategy_if_no_default_strategy_is_given()
    {
        $deployFile = (string) new DeployFile();

        $this->assertStringContainsString("set('strategy', 'basic');", $deployFile);
    }

    /** @test */
    function it_adds_custom_includes()
    {
        $deployFile = (string) new DeployFile([
            'include' => [
                'recipe/foo.php',
                'recipe/bar.php'
            ]
        ]);

        $this->assertStringContainsString("require 'recipe/foo.php';", $deployFile);
        $this->assertStringContainsString("require 'recipe/bar.php';", $deployFile);
    }

    /** @test */
    function it_adds_custom_strategies()
    {
        $deployFile = (string) new DeployFile([
            'strategies' => [
                'custom_a' => [
                    'deploy:prepare',
                    'deploy:release',
                    'deploy:symlink',
                ],
                'custom_b' => [
                    'deploy:update_code',
                    'hook:done',
                ],
            ]
        ]);

        $this->assertStringContainsString(
<<<EOD
desc('Custom A Strategy');
task('strategy:custom_a', [
    'deploy:prepare',
    'deploy:release',
    'deploy:symlink',
]);
EOD
        , $deployFile);

        $this->assertStringContainsString(
<<<EOD
desc('Custom B Strategy');
task('strategy:custom_b', [
    'deploy:update_code',
    'hook:done',
]);
EOD
        , $deployFile);
    }

    /** @test */
    function it_adds_global_options()
    {
        $deployFile = (string) new DeployFile([
            'options' => [
                'repository' => 'my/repo.git',
                'git_tty' => true,
                'foo' => [
                    'bar' => 0,
                    'baz' => 'My app',
                ]
            ]
        ]);

        $this->assertStringContainsString(
<<<EOD
set('repository', 'my/repo.git');
set('git_tty', true);
set('foo', [
    'bar' => 0,
    'baz' => 'My app',
]);
EOD
        , $deployFile);
    }

    /** @test */
    function it_does_not_evaluate_env_variables()
    {
        $deployFile = (string) new DeployFile([
            'options' => [
                'foo' => [
                    'bar' => "env('PLAIN_TEXT')",
                ],
            ],
        ]);

        $this->assertStringContainsString("'bar' => 'env(\'PLAIN_TEXT\')'", $deployFile);
    }

    /** @test */
    function it_adds_hosts()
    {
        $deployFile = (string) new DeployFile([
            'hosts' => [
                'elegon.io' => [
                    'hostname' => 'elegon.io',
                    'roles' => 'app',
                    'stage' => 'prod',
                    'user' => 'forge',
                    'port' => 22,
                    'configFile' => '~/.ssh/config',
                    'identityFile' => '~/.ssh/id_rsa',
                    'forwardAgent' => true,
                    'multiplexing' => true,
                    'sshOptions' => [
                        'UserKnownHostsFile' => '/dev/null',
                        'StrictHostKeyChecking' => 'no',
                    ],
                    'deploy_path' => '/home/forge/elegon.io',
                    'foo' => ['bar' => ['baz' => true]],
                ],
                'dev.elegon.io' => [
                    'stage' => 'staging',
                ],
            ]
        ]);

        $this->assertStringContainsString(
<<<EOD
host('elegon.io')
    ->hostname('elegon.io')
    ->roles('app')
    ->stage('prod')
    ->user('forge')
    ->port(22)
    ->configFile('~/.ssh/config')
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->set('deploy_path', '/home/forge/elegon.io')
    ->set('foo', [
        'bar' => [
            'baz' => true,
        ],
    ]);
EOD
        , $deployFile);

        $this->assertStringContainsString(
<<<EOD
host('dev.elegon.io')
    ->stage('staging');
EOD
        , $deployFile);
    }

    /** @test */
    function it_adds_localhost()
    {
        $deployFile = (string) new DeployFile([
            'localhost' => [
                'stage' => 'development',
                'user' => 'lorisleiva',
                'deploy_path' => '~/code/elegon/.build',
                'foo' => ['bar' => ['baz' => 4.2]],
            ]
        ]);

        $this->assertStringContainsString(
<<<EOD
localhost()
    ->stage('development')
    ->user('lorisleiva')
    ->set('deploy_path', '~/code/elegon/.build')
    ->set('foo', [
        'bar' => [
            'baz' => 4.2,
        ],
    ]);
EOD
        , $deployFile);
    }

    /** @test */
    function it_adds_hooks()
    {
        $deployFile = (string) new DeployFile([
            'hooks' => [
                'start'    => ['slack:notify'],
                'build'    => ['npm:install', 'npm:production'],
                'ready'    => ['artisan:cache:clear', 'artisan:migrate'],
                'done'     => ['fpm:reload'],
                'fail'     => ['slack:notify:failure'],
                'success'  => ['slack:notify:success'],
                'rollback' => ['fpm:reload'],
            ]
        ]);

        $this->assertStringContainsString(
<<<EOD
after('hook:start', 'slack:notify');
after('hook:build', 'npm:install');
after('hook:build', 'npm:production');
after('hook:ready', 'artisan:cache:clear');
after('hook:ready', 'artisan:migrate');
after('hook:done', 'fpm:reload');
after('deploy:failed', 'slack:notify:failure');
after('success', 'slack:notify:success');
after('hook:rollback', 'fpm:reload');
EOD
        , $deployFile);
    }

    /** @test */
    function it_stores_the_deploy_file_inside_the_laravel_deployer_package()
    {
        $deployFilePath = $this->generatedDeployPath();

        $this->assertFileDoesNotExist($deployFilePath);

        (new DeployFile())->store();

        $this->assertFileExists($deployFilePath);
        $this->exec('rm -rf ' . dirname($deployFilePath));
    }
}
