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

        $this->assertContains('Deploying HEAD on localhost', $output);
        $this->assertContains('Executing task deploy:prepare', $output);
        $this->assertContains('Executing task deploy:lock', $output);
        $this->assertContains('Executing task deploy:release', $output);
        $this->assertContains('Executing task deploy:update_code', $output);
        $this->assertContains('Executing task deploy:shared', $output);
        $this->assertContains('Executing task deploy:vendors', $output);
        $this->assertContains('Executing task deploy:writable', $output);
        $this->assertContains('Executing task artisan:storage:link', $output);
        $this->assertContains('Executing task artisan:view:clear', $output);
        $this->assertContains('Executing task artisan:cache:clear', $output);
        $this->assertContains('Executing task artisan:config:cache', $output);
        $this->assertContains('Executing task artisan:optimize', $output);
        $this->assertContains('Executing task deploy:symlink', $output);
        $this->assertContains('Executing task deploy:unlock', $output);
        $this->assertContains('Executing task cleanup', $output);
        $this->assertContains('Successfully deployed', $output);
        $this->assertSuccessfulDeployment();
    }
}