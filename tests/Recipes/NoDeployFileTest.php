<?php

namespace Lorisleiva\LaravelDeployer\Test\Recipes;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class NoDeployFileTest extends DeploymentTestCase
{
    protected $recipe = null;

    /** @test */
    function when_no_deploy_file_exists_it_should_warn_the_user()
    {
        $output = $this->artisan('deploy');

        $this->assertContains('deploy.php file not found', $output);
        $this->assertNotContains('Executing task deploy', $output);
    }
}