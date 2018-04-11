<?php

namespace Lorisleiva\LaravelDeployer\Test\Unit;

use Lorisleiva\LaravelDeployer\DeployFile;
use Lorisleiva\LaravelDeployer\Test\TestCase;

class DeployFileBuilderTest extends TestCase
{
    /** @test */
    function it_adds_laravel_deployer_recipe_by_default()
    {
        $deployFile = (string) new DeployFile();

        $this->assertContains("namespace Deployer;", $deployFile);
        $this->assertContains("require 'recipe/laravel-deployer.php';", $deployFile);
    }

    /** @test */
    function it_should_not_contain_more_than_two_consecutive_empty_lines()
    {
        $deployFile = (string) new DeployFile();

        $this->assertFalse((bool) preg_match("/\n{3,}/", $deployFile));
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

        $this->assertContains("require 'recipe/foo.php';", $deployFile);
        $this->assertContains("require 'recipe/bar.php';", $deployFile);
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

        $this->assertContains(
<<<EOD
desc('Custom A Strategy');
task('strategy:custom_a', [
    'deploy:prepare',
    'deploy:release',
    'deploy:symlink',
]);
EOD
        , $deployFile);

        $this->assertContains(
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
                    'bat' => "env('PLAIN_TEXT')",
                ]
            ]
        ]);

        $this->assertContains(
<<<EOD
set('repository', 'my/repo.git');
set('git_tty', true);
set('foo', [
    'bar' => 0,
    'baz' => 'My app',
    'bat' => 'env(\'PLAIN_TEXT\')',
]);
EOD
        , $deployFile);
    }

    /** @test */
    function it_adds_hosts()
    {
        $deployFile = (string) new DeployFile([
            'hosts' => [
                'elegon.io' => [
                    'stage' => 'prod',
                    'user' => 'forge',
                    'deploy_path' => '/home/forge/elegon.io',
                    'foo' => ['bar' => ['baz' => true]],
                ],
                'dev.elegon.io' => [
                    'stage' => 'staging',
                ],
            ]
        ]);

        $this->assertContains(
<<<EOD
host('elegon.io')
    ->stage('prod')
    ->user('forge')
    ->set('deploy_path', '/home/forge/elegon.io')
    ->set('foo', [
        'bar' => [
            'baz' => true,
        ],
    ]);
EOD
        , $deployFile);

        $this->assertContains(
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

        $this->assertContains(
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
                'start'   => ['slack:notify'],
                'build'   => ['npm:install', 'npm:production'],
                'ready'   => ['artisan:cache:clear', 'artisan:migrate'],
                'done'    => ['fpm:reload'],
                'fail'    => ['slack:notify:failure'],
                'success' => ['slack:notify:success'],
            ]
        ]);

        $this->assertContains(
<<<EOD
after('hook:start', 'slack:notify');
after('hook:build', 'npm:install');
after('hook:build', 'npm:production');
after('hook:ready', 'artisan:cache:clear');
after('hook:ready', 'artisan:migrate');
after('hook:done', 'fpm:reload');
after('deploy:fail', 'slack:notify:failure');
after('success', 'slack:notify:success');
EOD
        , $deployFile);
    }

    /** @test */
    function it_stores_the_deploy_file_inside_the_laravel_deployer_package()
    {
        $deployFilePath = __DIR__ . '/../../.build/deploy.php';

        $this->assertFileNotExists($deployFilePath);

        (new DeployFile())->store();

        $this->assertFileExists($deployFilePath);
        $this->exec('rm -rf ' . dirname($deployFilePath));
    }
}