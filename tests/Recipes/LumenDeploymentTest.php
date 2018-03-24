<?php

namespace Lorisleiva\LaravelDeployer\Test\Recipes;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class LumenDeploymentTest extends DeploymentTestCase
{
    protected $recipe = 'lumen';

    /** @test */
    function it_should_not_execute_artisan_commands_not_included_in_lumen_by_default()
    {
        $output = $this->artisan('deploy');

        $this->assertNotContains('Executing task artisan:storage:link', $output);
        $this->assertNotContains('Executing task artisan:view:clear', $output);
        $this->assertNotContains('Executing task artisan:config:cache', $output);
        $this->assertContains('Successfully deployed!', $output);
        $this->assertSuccessfulDeployment();
    }
}