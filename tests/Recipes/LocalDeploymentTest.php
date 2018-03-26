<?php

namespace Lorisleiva\LaravelDeployer\Test\Recipes;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class LocalDeploymentTest extends DeploymentTestCase
{
    protected $recipe = 'local';

    /** @test */
    function a_local_deployment_should_execute_all_local_tasks()
    {
        $output = $this->artisan('deploy:local');

        $this->assertContains('Executing task local:build', $output);
        $this->assertContains('Executing task local:upload', $output);
        $this->assertContains('Executing task local:cleanup', $output);
        $this->assertContains('Successfully deployed!', $output);
        $this->assertSuccessfulDeployment();
        $this->assertServerMiss('node_modules');
        $this->assertServerAssetsAreCompiled();
    }
}