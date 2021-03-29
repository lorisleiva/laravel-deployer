<?php

namespace Lorisleiva\LaravelDeployer\Test\Features;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class LogsTest extends DeploymentTestCase
{
    /** @test */
    function it_should_show_the_laravel_log_file_and_hide_error_stack_by_default()
    {
        $this->artisan('deploy');

        $authorized = [
            '[2018-04-17 14:02:53] production.ERROR: Error message',
            'Just a random log that has the word stacktrace',
            '#iamnotastackerrormkay',
            'Me neither #1',
        ];

        $unauthorized = [
            '[stacktrace]',
            '#9 /path/to/something(104): Error message',
            '#10 /path/to/something/else(54): Error message',
            '"}',
        ];

        foreach (array_merge($authorized, $unauthorized) as $log) {
            $this->runInRoot("echo '$log' >> shared/storage/logs/laravel.log");
        }

        $output = $this->artisan('logs');

        foreach ($authorized as $log) {
            $this->assertStringContainsString($log, $output);
        }

        foreach ($unauthorized as $log) {
            $this->assertStringNotContainsString($log, $output);
        }
    }
}
