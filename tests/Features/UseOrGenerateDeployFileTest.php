<?php

namespace Lorisleiva\LaravelDeployer\Test\Features;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class UseOrGenerateDeployFileTest extends DeploymentTestCase
{
    protected $configs = 'basic';

    /** @test */
    function the_config_file_generate_a_temporary_deploy_file_by_default()
    {
        $this->artisan('deploy:list');

        $this->assertFileExists($this->generatedDeployPath());
    }

    /** @test */
    function the_custom_deployer_file_option_is_used_if_provided()
    {
        $this->fakeDeployFile('custom_root_deploy_file', 'my_deploy.php');

        $this->updateConfigFile(function ($content) {
            return str_replace('];', "\n'custom_deployer_file' => 'my_deploy.php',\n];", $content);
        });

        $output = $this->artisan('deploy');

        $this->assertStringContainsString('strategy:custom_root_deploy_file', $output);
        $this->assertFileDoesNotExist($this->generatedDeployPath());
    }

    /** @test */
    function the_root_deploy_php_file_is_used_when_no_config_file_exists()
    {
        $this->fakeDeployFile('root_deploy_file', 'deploy.php');
        $this->runInRepository('rm config/deploy.php');

        $output = $this->artisan('deploy');

        $this->assertStringContainsString('strategy:root_deploy_file', $output);
        $this->assertFileDoesNotExist($this->generatedDeployPath());
    }

    /** @test */
    function when_a_deploy_file_is_given_as_an_option_it_should_be_prioritized()
    {
        $this->fakeDeployFile('deploy_file_given_as_an_option', 'option_deploy.php');

        $output = $this->artisan('deploy', [
            '--file' => 'option_deploy.php',
        ]);

        $this->assertStringContainsString('strategy:deploy_file_given_as_an_option', $output);
    }

    /** @test */
    function when_no_root_deploy_php_nor_config_file_nor_file_option_exists_throw_an_error()
    {
        $this->runInRepository('rm config/deploy.php');

        $output = $this->artisan('deploy');

        $this->assertStringContainsString('config/deploy.php file not found', $output);
        $this->assertStringNotContainsString('Executing task deploy', $output);
    }

    public function fakeDeployFile($strategy, $path)
    {
        file_put_contents(static::REPOSITORY . '/' . $path, "<?php 
            namespace Deployer;
            require 'recipe/laravel-deployer.php';
            localhost()->set('deploy_path', '".static::SERVER."');
            set('strategy', '$strategy');
            task('show:strategy:$strategy', function() {});
            task('strategy:$strategy', ['show:strategy:$strategy']);
        ");
    }
}
