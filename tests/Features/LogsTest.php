<?php

namespace Lorisleiva\LaravelDeployer\Test\Commands;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class LogsTest extends DeploymentTestCase
{
    /** @test */
    function it_should_show_the_laravel_log_file_and_hide_error_stack_by_default()
    {
        $this->artisan('deploy');
        $this->runInRoot('echo "Error A" >> shared/storage/logs/laravel.log');
        $this->runInRoot('echo "#1 stack" >> shared/storage/logs/laravel.log');
        $this->runInRoot('echo "#56 stack" >> shared/storage/logs/laravel.log');
        $this->runInRoot('echo "#1293 stack" >> shared/storage/logs/laravel.log');
        $this->runInRoot('echo "Error B" >> shared/storage/logs/laravel.log');
        $this->runInRoot('echo "Error C" >> shared/storage/logs/laravel.log');
        $this->runInRoot('echo "#iamnotastackerrormkay" >> shared/storage/logs/laravel.log');
        $this->runInRoot('echo "Me neither #1" >> shared/storage/logs/laravel.log');

        $output = $this->artisan('logs');

        $this->assertContains('Error A', $output);
        $this->assertNotContains('#1 stack', $output);
        $this->assertNotContains('#2 stack', $output);
        $this->assertNotContains('#3 stack', $output);
        $this->assertContains('Error B', $output);
        $this->assertContains('Error C', $output);
        $this->assertContains('#iamnotastackerrormkay', $output);
        $this->assertContains('Me neither #1', $output);
    }
}