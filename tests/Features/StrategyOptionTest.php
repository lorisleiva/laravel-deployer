<?php

namespace Lorisleiva\LaravelDeployer\Test\Features;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class StrategyOptionTest extends DeploymentTestCase
{
    protected $configs = 'basic';

    /** @test */
    function a_rollback_with_no_previous_release_should_do_nothing_but_warn_user()
    {
        $this->assertContains('with basic strategy', $this->deployInfo('basic'));
        $this->assertContains('with firstdeploy strategy', $this->deployInfo('firstdeploy'));
        $this->assertContains('with local strategy', $this->deployInfo('local'));
        $this->assertContains('with pull strategy', $this->deployInfo('pull'));
    }

    protected function deployInfo($strategy)
    {
        return $this->artisan('deploy:run', [
            'task' => 'deploy:info_debug',
            '-s' => $strategy,
        ]);
    }
}