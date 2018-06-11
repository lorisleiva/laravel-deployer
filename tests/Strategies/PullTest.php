<?php

namespace Lorisleiva\LaravelDeployer\Test\Strategies;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class PullTest extends DeploymentTestCase
{
    protected $configs = 'basic';

    /** @test */
    function a_pull_deployment_should_fail_when_current_directory_missing()
    {
        $output = $this->artisan('deploy', ['-s' => 'pull']);

        $this->assertContains('Executing task deploy:failed', $output);
        $this->assertNotContains('Successfully deployed', $output);
    }

    /** @test */
    function a_pull_deployment_should_not_create_a_new_release_nor_build_assets()
    {
        // Given we deployed it once before.
        $this->artisan('deploy');
        $this->assertEquals(1, $this->runInRoot("ls -A1 releases | wc -l"));

        // And we're sure unicorn.txt does not exists on the release.
        $this->assertServerMiss('unicorn.txt');

        // When we add and commit unicorn.txt.
        $this->runInRepository('touch unicorn.txt');
        $this->commitChanges();

        // And we do a successful deploy with the `pull` strategy
        $output = $this->artisan('deploy', ['-s' => 'pull']);
        $this->assertContains('Successfully deployed', $output);

        // Then the unicorn.txt is present on the current directory.
        $this->assertServerHas('unicorn.txt');

        // And no new release has been created
        $this->assertEquals(1, $this->runInRoot("ls -A1 releases | wc -l"));

        // And no assets have been built
        $this->assertNotContains('Executing task npm:install', $output);
        $this->assertNotContains('Executing task npm:development', $output);
        $this->assertNotContains('Executing task npm:production', $output);
    }
}
