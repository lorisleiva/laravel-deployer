<?php

namespace Lorisleiva\LaravelDeployer\Test\Unit;

use Lorisleiva\LaravelDeployer\ConfigFileBuilder;
use Lorisleiva\LaravelDeployer\Test\TestCase;

class ConfigFileBuilderTest extends TestCase
{
    /** @test */
    function it_can_set_the_repository_url()
    {
        $config = (new ConfigFileBuilder)
            ->set('options.repository', 'ssh://git@github.com:lorisleiva/laravel-deployer.git')
            ->build();

        $this->assertArraySubset(
            ['options' => [
                'repository' => 'ssh://git@github.com:lorisleiva/laravel-deployer.git',
            ]],
            $config->toArray()
        );
    }

    /** @test */
    function it_can_set_nested_configuration_arrays()
    {
        $config = (new ConfigFileBuilder)
            ->set('options.foo', [
                'bar' => ['baz' => true, 'bat' => 0, 'bal' => 42, 'baj' => 'text'],
                'indexedArray' => ['one', 2, 'three', null],
            ])
            ->build();

        $this->assertArraySubset(
            ['options' => [
                'foo' => [
                    'bar' => [
                        'baz' => true,
                        'bat' => 0,
                        'bal' => 42,
                        'baj' => 'text',
                    ],
                    'indexedArray' => [
                        'one',
                        2,
                        'three',
                        null,
                    ],
                ],
            ]],
            $config->toArray()
        );
    }

    /** @test */
    function it_can_set_the_hostname()
    {
        $config = (new ConfigFileBuilder)
            ->setHost('name', 'elegon.io')
            ->build();

        $this->assertArraySubset(
            ['hosts' => [
                'elegon.io' => [],
            ]],
            $config->toArray()
        );
    }

    /** @test */
    function it_can_set_host_options()
    {
        $config = (new ConfigFileBuilder)
            ->setHost('name', 'elegon.io')
            ->setHost('deploy_path', '/home/forge/elegon.io')
            ->setHost('user', 'lorisleiva')
            ->setHost('stage', 'prod')
            ->setHost('foo', 'bar')
            ->build();

        $this->assertArraySubset(
            ['hosts' => [
                'elegon.io' => [
                    'deploy_path' => '/home/forge/elegon.io',
                    'user' => 'lorisleiva',
                    'stage' => 'prod',
                    'foo' => 'bar',
                ],
            ]],
            $config->toArray()
        );
    }

    /** @test */
    function it_uses_default_laravel_ready_hooks()
    {
        $config = (new ConfigFileBuilder)->build();

        $this->assertArraySubset(
            ['hooks' => [
                'ready' => [
                    'artisan:storage:link',
                    'artisan:view:clear',
                    'artisan:cache:clear',
                    'artisan:config:cache',
                    'artisan:optimize',
                ],
            ]],
            $config->toArray()
        );
    }

    /** @test */
    function it_can_be_set_to_use_npm()
    {
        $config = (new ConfigFileBuilder)
            ->add('hooks.build', 'npm:install')
            ->add('hooks.build', 'npm:development')
            ->build();

        $this->assertArraySubset(
            ['hooks' => [
                'build' => [
                    'npm:install',
                    'npm:development',
                ],
            ]],
            $config->toArray()
        );
    }

    /** @test */
    function it_can_be_set_to_use_migrations_and_terminate_horizon()
    {
        $config = (new ConfigFileBuilder)
            ->add('hooks.ready', 'artisan:migrate')
            ->add('hooks.ready', 'artisan:horizon:terminate')
            ->build();

        $this->assertArraySubset(
            ['hooks' => [
                'ready' => [
                    'artisan:storage:link',
                    'artisan:view:clear',
                    'artisan:cache:clear',
                    'artisan:config:cache',
                    'artisan:optimize',
                    'artisan:migrate',
                    'artisan:horizon:terminate',
                ],
            ]],
            $config->toArray()
        );
    }

    /** @test */
    function it_can_set_to_use_forge_to_provide_some_better_defaults()
    {
        $config = (new ConfigFileBuilder)
            ->setHost('name', 'elegon.io')
            ->useForge()
            ->build();

        $this->assertArraySubset(
            [
                'hosts' => [
                    'elegon.io' => [
                        'deploy_path' => '/home/forge/elegon.io',
                        'user' => 'forge',
                    ],
                ],
                'hooks' => [
                    'done' => [
                        'fpm:reload',
                    ],
                ],
                'options' => [
                    'php_fpm_service' => 'php' . ConfigFileBuilder::DEFAULT_PHP_VERSION . '-fpm'
                ],
            ],
            $config->toArray()
        );
    }

    /** @test */
    function it_can_set_to_reload_php_fpm_after_each_deployment()
    {
        $config = (new ConfigFileBuilder)
            ->reloadFpm('7.0')
            ->build();

        $this->assertArraySubset(
            [
                'hooks' => [
                    'done' => [
                        'fpm:reload',
                    ],
                ],
                'options' => [
                    'php_fpm_service' => 'php7.0-fpm'
                ],
            ],
            $config->toArray()
        );
    }

    /** @test */
    function it_returns_current_values_of_configs()
    {
        $repo = (new ConfigFileBuilder)
            ->set('options.repository', 'some/repository.git')
            ->get('options.repository');

        $this->assertEquals('some/repository.git', $repo);
    }
}
