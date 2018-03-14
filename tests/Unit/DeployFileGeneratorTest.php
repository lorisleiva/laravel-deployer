<?php

namespace Lorisleiva\LaravelDeployer\Test\Unit;

use Lorisleiva\LaravelDeployer\DeployFileGenerator;
use Lorisleiva\LaravelDeployer\Test\TestCase;

class DeployFileGeneratorTest extends TestCase
{
    /** @test */
    function default_parsed_stub_should_not_contain_any_variables()
    {
        $stub = (new DeployFileGenerator)->getParsedStub();

        $this->assertFalse((bool) preg_match("/{{.+}}/", $stub));
    }

    /** @test */
    function stub_should_not_have_trailing_empty_lines()
    {
        $stub = (new DeployFileGenerator)->getParsedStub();

        $this->assertFalse((bool) preg_match("/\n$/", $stub));
    }

    /** @test */
    function stub_should_not_contain_more_than_two_consecutive_empty_lines()
    {
        $stub = (new DeployFileGenerator)->getParsedStub();

        $this->assertFalse((bool) preg_match("/\n{3,}/", $stub));
    }

    /** @test */
    function it_can_set_the_application_name()
    {
        $stub = (new DeployFileGenerator)
            ->application('FooBar')
            ->getParsedStub();

        $this->assertContains("set('application', 'FooBar')", $stub);
        $this->assertContains("'foo_bar:helloworld'", $stub);
    }

    /** @test */
    function it_can_set_the_repository_url()
    {
        $stub = (new DeployFileGenerator)
            ->repository('ssh://git@github.com:lorisleiva/laravel-deployer.git')
            ->getParsedStub();

        $this->assertContains(
            "set('repository', 'ssh://git@github.com:lorisleiva/laravel-deployer.git')", 
            $stub
        );
    }

    /** @test */
    function it_can_choose_to_override_files_and_directories_instead_of_adding_them()
    {
        $stub = (new DeployFileGenerator)->getParsedStub();

        $this->assertContains("add('shared_files', []);", $stub);
        $this->assertContains("add('shared_dirs', []);", $stub);
        $this->assertContains("add('writable_dirs', []);", $stub);

        $stub = (new DeployFileGenerator)
            ->override('shared_files')
            ->override('shared_dirs')
            ->override('writable_dirs')
            ->getParsedStub();

        $this->assertContains("set('shared_files', [ /* List your shared files here. */ ]);", $stub);
        $this->assertContains("set('shared_dirs', [ /* List your shared directories here. */ ]);", $stub);
        $this->assertContains("set('writable_dirs', [ /* List your writable directories here. */ ]);", $stub);
    }

    /** @test */
    function it_can_set_the_hostname()
    {
        $stub = (new DeployFileGenerator)
            ->hostname('elegon.io')
            ->getParsedStub();

        $this->assertContains("host('elegon.io')", $stub);
    }

    /** @test */
    function it_can_set_the_host_stage()
    {
        $stub = (new DeployFileGenerator)
            ->stage('prod')
            ->getParsedStub();

        $this->assertContains("\n    ->stage('prod')", $stub);
    }

    /** @test */
    function it_can_set_the_host_deployment_path()
    {
        $stub = (new DeployFileGenerator)
            ->deploymentPath('/home/forge/elegon.io')
            ->getParsedStub();

        $this->assertContains(
            "\n    ->set('deploy_path', '/home/forge/elegon.io')", 
            $stub
        );
    }

    /** @test */
    function it_can_set_the_host_user()
    {
        $stub = (new DeployFileGenerator)
            ->user('lorisleiva')
            ->getParsedStub();

        $this->assertContains("\n    ->user('lorisleiva')", $stub);
    }

    /** @test */
    function it_can_be_set_to_use_npm()
    {
        $stub = (new DeployFileGenerator)
            ->useNpm('development')
            ->getParsedStub();

        $this->assertContains("after('deploy:update_code', 'npm:install');", $stub);
        $this->assertContains("after('npm:install', 'npm:development');", $stub);
    }

    /** @test */
    function it_can_be_set_to_use_migrations()
    {
        $stub = (new DeployFileGenerator)
            ->migrate()
            ->getParsedStub();

        $this->assertContains("before('deploy:symlink', 'artisan:migrate');", $stub);
    }

    /** @test */
    function it_can_be_set_to_terminate_horizon()
    {
        $stub = (new DeployFileGenerator)
            ->terminateHorizon()
            ->getParsedStub();

        $this->assertContains("before('deploy:symlink', 'artisan:horizon:terminate')", $stub);
    }

    /** @test */
    function when_set_to_migrate_and_terminate_horizon_it_hooks_horizon_after_migrate()
    {
        // From one way...
        $stub = (new DeployFileGenerator)
            ->migrate()
            ->terminateHorizon()
            ->getParsedStub();

        $this->assertContains("before('deploy:symlink', 'artisan:migrate');", $stub);
        $this->assertContains("after('artisan:migrate', 'artisan:horizon:terminate')", $stub);

        // ... or another.
        $stub = (new DeployFileGenerator)
            ->terminateHorizon()
            ->migrate()
            ->getParsedStub();

        $this->assertContains("before('deploy:symlink', 'artisan:migrate');", $stub);
        $this->assertContains("after('artisan:migrate', 'artisan:horizon:terminate')", $stub);
    }

    /** @test */
    function when_no_hooks_are_used_it_display_the_migration_hook_commented()
    {
        $stubEmpty = (new DeployFileGenerator)->getParsedStub();
        $stubNpm = (new DeployFileGenerator)->useNpm()->getParsedStub();
        $stubMigrate = (new DeployFileGenerator)->migrate()->getParsedStub();
        $stubHorizon = (new DeployFileGenerator)->terminateHorizon()->getParsedStub();
        $stubFpm = (new DeployFileGenerator)->reloadFpm()->getParsedStub();

        $this->assertContains("// before('deploy:symlink', 'artisan:migrate');", $stubEmpty);
        $this->assertNotContains("// before('deploy:symlink', 'artisan:migrate');", $stubNpm);
        $this->assertNotContains("// before('deploy:symlink', 'artisan:migrate');", $stubMigrate);
        $this->assertNotContains("// before('deploy:symlink', 'artisan:migrate');", $stubHorizon);
        $this->assertNotContains("// before('deploy:symlink', 'artisan:migrate');", $stubFpm);
    }

    /** @test */
    function it_can_set_to_create_a_localhost_version_to_be_called_by_the_server_itself()
    {
        $stub = (new DeployFileGenerator)
            ->localhost()
            ->getParsedStub();

        $this->assertContains("set('default_stage', 'prod');", $stub);

        $this->assertContains(
<<<EOD
host('example.com')
    ->stage('prod')
    ->set('deploy_path', '/var/www/html');
EOD
        , $stub);

        $this->assertContains(
<<<EOD
localhost()
    ->stage('local')
    ->set('deploy_path', '/var/www/html')
    ->user('root')
    ->set('git_tty', false);
EOD
        , $stub);
    }

    /** @test */
    function it_can_set_to_use_forge_to_provide_some_better_defaults()
    {
        $stub = (new DeployFileGenerator)
            ->useForge()
            ->hostname('elegon.io')
            ->getParsedStub();

        $this->assertContains("set('default_stage', 'prod');", $stub);

        $this->assertContains(
<<<EOD
host('elegon.io')
    ->stage('prod')
    ->set('deploy_path', '/home/forge/elegon.io')
    ->user('forge');
EOD
        , $stub);

        $this->assertContains(
<<<EOD
localhost()
    ->stage('local')
    ->set('deploy_path', '/home/forge/elegon.io')
    ->user('forge')
    ->set('git_tty', false);
EOD
        , $stub);
    }

    /** @test */
    function it_returns_current_values_of_replacement_variables()
    {
        $appName = (new DeployFileGenerator)
            ->application('Elegon')
            ->get('application');

        $this->assertEquals('Elegon', $appName);
    }

    /** @test */
    function it_can_be_set_to_use_fpm_reloading()
    {
        $stub = (new DeployFileGenerator)
            ->reloadFpm()
            ->getParsedStub();

        $this->assertContains("set('php_fpm_service', 'php7.1-fpm');", $stub);
        $this->assertContains("after('cleanup', 'fpm:reload');", $stub);
    }

    /** @test */
    function it_automatically_use_fpm_reloading_when_using_forge()
    {
        $stub = (new DeployFileGenerator)
            ->useForge()
            ->getParsedStub();

        $this->assertContains("set('php_fpm_service', 'php7.1-fpm');", $stub);
        $this->assertContains("after('cleanup', 'fpm:reload');", $stub);
    }
}