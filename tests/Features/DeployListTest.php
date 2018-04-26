<?php

namespace Lorisleiva\LaravelDeployer\Test\Commands;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class DeployListTest extends DeploymentTestCase
{
    /** @test */
    function it_should_list_all_available_tasks()
    {
        $output = $this->artisan('deploy:list');

        // Laravel Deployer
        $this->assertContains('firstdeploy:cleanup', $output);
        $this->assertContains('firstdeploy:shared', $output);
        $this->assertContains('fpm:reload', $output);
        $this->assertContains('npm:development', $output);
        $this->assertContains('npm:production', $output);
        $this->assertContains('local:build', $output);
        $this->assertContains('local:upload', $output);
        $this->assertContains('local:cleanup', $output);

        // Npm recipe
        $this->assertContains('npm:install', $output);

        // Artisan
        $this->assertContains('artisan:cache:clear', $output);
        $this->assertContains('artisan:config:cache', $output);
        $this->assertContains('artisan:db:seed', $output);
        $this->assertContains('artisan:down', $output);
        $this->assertContains('artisan:horizon:terminate', $output);
        $this->assertContains('artisan:migrate', $output);
        $this->assertContains('artisan:migrate:fresh', $output);
        $this->assertContains('artisan:migrate:rollback', $output);
        $this->assertContains('artisan:migrate:status', $output);
        $this->assertContains('artisan:optimize', $output);
        $this->assertContains('artisan:queue:restart', $output);
        $this->assertContains('artisan:route:cache', $output);
        $this->assertContains('artisan:storage:link', $output);
        $this->assertContains('artisan:up', $output);
        $this->assertContains('artisan:view:clear', $output);

        // Config
        $this->assertContains('config:current', $output);
        $this->assertContains('config:dump', $output);
        $this->assertContains('config:hosts', $output);

        // Debug
        $this->assertContains('debug:task', $output);

        // Deploy
        $this->assertContains('deploy:clear_paths', $output);
        $this->assertContains('deploy:copy_dirs', $output);
        $this->assertContains('deploy:lock', $output);
        $this->assertContains('deploy:prepare', $output);
        $this->assertContains('deploy:release', $output);
        $this->assertContains('deploy:shared', $output);
        $this->assertContains('deploy:symlink', $output);
        $this->assertContains('deploy:unlock', $output);
        $this->assertContains('deploy:update_code', $output);
        $this->assertContains('deploy:vendors', $output);
        $this->assertContains('deploy:writable', $output);
    }
}