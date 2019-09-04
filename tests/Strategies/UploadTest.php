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

        $this->assertStringContainsString('Executing task upload', $output);
        $this->assertStringContainsString('Successfully deployed', $output);
        $this->assertSuccessfulDeployment();
        $this->assertServerMiss('node_modules');
        $this->assertServerAssetsAreCompiled();
    }

    /** @test */
    function the_vendor_folder_should_be_uploaded_when_option_upload_vendors_is_true()
    {
        // Mock npm install and npm run development.
        $this->runInRepository('mkdir -p vendor');
        $this->runInRepository('echo "I have been build manually and uploaded" > vendor/foobar.txt');

        $output = $this->artisan('deploy', ['-s' => 'upload', '-o' => 'upload_vendors=true']);

        $this->assertSuccessfulDeployment();
        $this->assertServerFilesEquals([
            'vendor/foobar.txt' => 'I have been build manually and uploaded'
        ]);
    }
}