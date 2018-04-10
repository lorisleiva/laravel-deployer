<?php

namespace Lorisleiva\LaravelDeployer\Test\Unit;

use Lorisleiva\LaravelDeployer\ConfigFileGenerator;
use Lorisleiva\LaravelDeployer\Test\TestCase;

class ConfigFileGeneratorTest extends TestCase
{
    /** @test */
    function default_parsed_stub_should_not_contain_any_variables()
    {
        $stub = (new ConfigFileGenerator)->getParsedStub();

        $this->assertFalse((bool) preg_match("/{{.+}}/", $stub));
    }

    /** @test */
    function it_can_set_the_repository_url()
    {
        $stub = (new ConfigFileGenerator)
            ->set('options.repository', 'ssh://git@github.com:lorisleiva/laravel-deployer.git')
            ->getParsedStub();

        $this->assertContains(
            "'repository' => 'ssh://git@github.com:lorisleiva/laravel-deployer.git'", 
            $stub
        );
    }

    /** @test */
    function it_can_set_nested_configuration_arrays()
    {    
        $stub = (new ConfigFileGenerator)
            ->set('options.foo', [
                'bar' => ['baz' => true, 'bat' => 0, 'bal' => 42, 'baj' => 'text'],
                'indexedArray' => ['one', 2, 'three', null],
            ])
            ->getParsedStub();

        $this->assertContains(
<<<EOD
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
EOD
        , $stub);
    }

    /** @test */
    function it_can_set_the_hostname()
    {
        $stub = (new ConfigFileGenerator)
            ->setHost('name', 'elegon.io')
            ->getParsedStub();

        $this->assertContains(
<<<EOD
    'hosts' => [
        'elegon.io' => [
EOD
        , $stub);
    }

    /** @test */
    function it_can_set_the_host_stage()
    {
        $stub = (new ConfigFileGenerator)
            ->setHost('name', 'elegon.io')
            ->setHost('deploy_path', '/home/forge/elegon.io')
            ->setHost('user', 'lorisleiva')
            ->setHost('stage', 'prod')
            ->getParsedStub();

        $this->assertContains(
<<<EOD
    'hosts' => [
        'elegon.io' => [
            'deploy_path' => '/home/forge/elegon.io',
            'user' => 'lorisleiva',
            'stage' => 'prod',
        ],
    ],
EOD
        , $stub);
    }

    /** @test */
    function it_uses_default_laravel_ready_hooks()
    {
        $stub = (new ConfigFileGenerator)->getParsedStub();

        $this->assertContains(
<<<EOD
        'ready' => [
            'artisan:storage:link',
            'artisan:view:clear',
            'artisan:cache:clear',
            'artisan:config:cache',
            'artisan:optimize',
        ],
EOD
        , $stub);
    }

    /** @test */
    function it_can_be_set_to_use_npm()
    {
        $stub = (new ConfigFileGenerator)
            ->add('hooks.build', 'npm:install')
            ->add('hooks.build', 'npm:development')
            ->getParsedStub();

        $this->assertContains(
<<<EOD
        'build' => [
            'npm:install',
            'npm:development',
        ],
EOD
        , $stub);
    }

    /** @test */
    function it_can_be_set_to_use_migrations_and_terminate_horizon()
    {
        $stub = (new ConfigFileGenerator)
            ->add('hooks.ready', 'artisan:migrate')
            ->add('hooks.ready', 'artisan:horizon:terminate')
            ->getParsedStub();

        $this->assertContains(
<<<EOD
        'ready' => [
            'artisan:storage:link',
            'artisan:view:clear',
            'artisan:cache:clear',
            'artisan:config:cache',
            'artisan:optimize',
            'artisan:migrate',
            'artisan:horizon:terminate',
        ],
EOD
        , $stub);
    }

    /** @test */
    function it_can_set_to_use_forge_to_provide_some_better_defaults()
    {
        $stub = (new ConfigFileGenerator)
            ->setHost('name', 'elegon.io')
            ->useForge()
            ->getParsedStub();

        $this->assertContains(
<<<EOD
    'hosts' => [
        'elegon.io' => [
            'deploy_path' => '/home/forge/elegon.io',
            'user' => 'forge',
        ],
    ],
EOD
        , $stub);

        $this->assertContains(
<<<EOD
        'done' => [
            'fpm:reload',
        ],
EOD
        , $stub);

        $this->assertContains("'php_fpm_service' => 'php7.1-fpm'", $stub);
    }

    /** @test */
    function it_returns_current_values_of_configs()
    {
        $repo = (new ConfigFileGenerator)
            ->set('options.repository', 'some/repository.git')
            ->get('options.repository');

        $this->assertEquals('some/repository.git', $repo);
    }
}