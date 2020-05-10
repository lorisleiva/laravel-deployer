<?php

namespace Lorisleiva\LaravelDeployer\Test\Strategies;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class BasicTest extends DeploymentTestCase
{
    protected $configs = 'basic';

    /** @test */
    function a_basic_deployment_should_execute_all_default_tasks()
    {
        $output = $this->artisan('deploy');

        $this->assertStringContainsString('Deploying HEAD on localhost', $output);
        $this->assertStringContainsString('Executing task deploy:prepare', $output);
        $this->assertStringContainsString('Executing task deploy:lock', $output);
        $this->assertStringContainsString('Executing task deploy:release', $output);
        $this->assertStringContainsString('Executing task deploy:update_code', $output);
        $this->assertStringContainsString('Executing task deploy:shared', $output);
        $this->assertStringContainsString('Executing task deploy:vendors', $output);
        $this->assertStringContainsString('Executing task deploy:writable', $output);
        $this->assertStringContainsString('Executing task artisan:storage:link', $output);
        $this->assertStringContainsString('Executing task artisan:view:clear', $output);
        $this->assertStringContainsString('Executing task artisan:config:cache', $output);
        $this->assertStringContainsString('Executing task artisan:optimize', $output);
        $this->assertStringContainsString('Executing task deploy:symlink', $output);
        $this->assertStringContainsString('Executing task deploy:unlock', $output);
        $this->assertStringContainsString('Executing task cleanup', $output);
        $this->assertStringContainsString('Successfully deployed', $output);
        $this->assertSuccessfulDeployment();
    }
}
