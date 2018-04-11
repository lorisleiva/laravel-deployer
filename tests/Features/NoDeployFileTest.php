<?php

namespace Lorisleiva\LaravelDeployer\Test\Features;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class NoDeployFileTest extends DeploymentTestCase
{
    protected $configs = null;

    /** @test */
    function when_no_deploy_file_exists_it_should_warn_the_user()
    {
        $output = $this->artisan('deploy');

        $this->assertContains('config/deploy.php file not found', $output);
        $this->assertNotContains('Executing task deploy', $output);
    }
}