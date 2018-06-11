<?php

namespace Lorisleiva\LaravelDeployer\Test\Strategies;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class UploadTest extends DeploymentTestCase
{
    protected $configs = 'basic';

    /** @test */
    function an_upload_deployment_should_execute_all_upload_tasks()
    {
        // Mock npm install and npm run development.
        $this->runInRepository('mkdir -p node_modules/vendor/package');
        $this->runInRepository('echo "compiled app.css" > public/css/app.css');
        $this->runInRepository('echo "compiled app.js" > public/js/app.js');

        $output = $this->artisan('deploy', ['-s' => 'upload']);

        $this->assertContains('Executing task upload', $output);
        $this->assertContains('Successfully deployed', $output);
        $this->assertSuccessfulDeployment();
        $this->assertServerMiss('node_modules');
        $this->assertServerAssetsAreCompiled();
    }
}
