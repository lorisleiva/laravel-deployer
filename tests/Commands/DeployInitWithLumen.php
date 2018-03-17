<?php

namespace Lorisleiva\LaravelDeployer\Test\Commands;

use Illuminate\Contracts\Console\Kernel;
use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class DeployInitWithLumenTest extends DeploymentTestCase
{
    /** @test */
    function it_requires_lumen_deployer_recipe_for_lumen_applications()
    {
        $this->artisan('deploy:init', [ '-n' ]);

        $deployFile = $this->runInRepository('cat deploy.php');

        $this->assertContains("require 'recipe/lumen-deployer.php';", $deployFile);
    }

    /**
     * @override
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(Kernel::class, LumenKernel::class);
    }
}

class LumenKernel extends \Orchestra\Testbench\Console\Kernel implements Kernel
{
    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            return $this->artisan = (new \Illuminate\Console\Application(
                $this->app, $this->events, 'Lumen (5.6.2)'
            ))->resolveCommands($this->commands);
        }

        return $this->artisan;
    }
}