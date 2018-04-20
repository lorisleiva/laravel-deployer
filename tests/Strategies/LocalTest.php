<?php

namespace Lorisleiva\LaravelDeployer\Test\Strategies;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class LocalTest extends DeploymentTestCase
{
    protected $configs = 'basic';

    /** @test */
    function a_local_deployment_should_execute_all_local_tasks()
    {
        $output = $this->artisan('deploy', ['-s' => 'local']);

        $this->assertContains('Executing task local:build', $output);
        $this->assertContains('Executing task local:upload', $output);
        $this->assertContains('Executing task local:cleanup', $output);
        $this->assertContains('Successfully deployed', $output);
        $this->assertSuccessfulDeployment();
        $this->assertServerMiss('node_modules');
        $this->assertServerAssetsAreCompiled();
    }
}