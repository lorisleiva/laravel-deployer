<?php

namespace Lorisleiva\LaravelDeployer\Test\Features;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class DeployRollbackTest extends DeploymentTestCase
{
    protected $configs = 'basic';

    /** @test */
    function a_rollback_with_no_previous_release_should_do_nothing_but_warn_the_user()
    {
        $output = $this->artisan('deploy:rollback');

        $this->assertStringContainsString('Executing task rollback', $output);
        $this->assertStringContainsString('No more releases you can revert to', $output);
    }

    /** @test */
    function a_rollback_should_symlink_to_the_previous_release()
    {
        /* 1st release */
        
        $this->runInRepository('touch unicorn.txt');
        $this->commitChanges();

        $this->artisan('deploy');

        $this->assertSuccessfulDeployment();
        $this->assertServerHas('unicorn.txt');

        /* 2nd release */

        $this->runInRepository('rm unicorn.txt');
        $this->commitChanges();

        $this->artisan('deploy');

        $this->assertSuccessfulDeployment();
        $this->assertServerMiss('unicorn.txt');

        /* Rollback */

        $output = $this->artisan('deploy:rollback');

        $this->assertSuccessfulDeployment();
        $this->assertServerHas('unicorn.txt');
        $this->assertStringContainsString('Executing task fpm:reload', $output);
    }
}