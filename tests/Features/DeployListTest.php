<?php

namespace Lorisleiva\LaravelDeployer\Test\Features;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

class DeployListTest extends DeploymentTestCase
{
    /** @test */
    function it_should_list_all_available_tasks()
    {
        $output = $this->artisan('deploy:list');

        // Laravel Deployer
        $this->assertStringContainsString('firstdeploy:cleanup', $output);
        $this->assertStringContainsString('firstdeploy:shared', $output);
        $this->assertStringContainsString('fpm:reload', $output);
        $this->assertStringContainsString('npm:development', $output);
        $this->assertStringContainsString('npm:production', $output);
        $this->assertStringContainsString('local:build', $output);
        $this->assertStringContainsString('local:upload', $output);
        $this->assertStringContainsString('local:cleanup', $output);

        // Npm recipe
        $this->assertStringContainsString('npm:install', $output);

        // Artisan
        $this->assertStringContainsString('artisan:cache:clear', $output);
        $this->assertStringContainsString('artisan:config:cache', $output);
        $this->assertStringContainsString('artisan:config:clear', $output);
        $this->assertStringContainsString('artisan:db:seed', $output);
        $this->assertStringContainsString('artisan:down', $output);
        $this->assertStringContainsString('artisan:horizon:assets', $output);
        $this->assertStringContainsString('artisan:horizon:publish', $output);
        $this->assertStringContainsString('artisan:horizon:terminate', $output);
        $this->assertStringContainsString('artisan:migrate', $output);
        $this->assertStringContainsString('artisan:migrate:fresh', $output);
        $this->assertStringContainsString('artisan:migrate:rollback', $output);
        $this->assertStringContainsString('artisan:migrate:status', $output);
        $this->assertStringContainsString('artisan:optimize', $output);
        $this->assertStringContainsString('artisan:optimize:clear', $output);
        $this->assertStringContainsString('artisan:queue:restart', $output);
        $this->assertStringContainsString('artisan:route:cache', $output);
        $this->assertStringContainsString('artisan:storage:link', $output);
        $this->assertStringContainsString('artisan:up', $output);
        $this->assertStringContainsString('artisan:view:clear', $output);
        $this->assertStringContainsString('artisan:view:cache', $output);
        $this->assertStringContainsString('artisan:telescope:clear', $output);
        $this->assertStringContainsString('artisan:telescope:prune', $output);
        $this->assertStringContainsString('artisan:telescope:publish', $output);
        $this->assertStringContainsString('artisan:nova:publish', $output);
        $this->assertStringContainsString('artisan:event:clear', $output);
        $this->assertStringContainsString('artisan:event:cache', $output);

        // Config
        $this->assertStringContainsString('config:current', $output);
        $this->assertStringContainsString('config:dump', $output);
        $this->assertStringContainsString('config:hosts', $output);

        // Debug
        $this->assertStringContainsString('debug:task', $output);

        // Deploy
        $this->assertStringContainsString('deploy:clear_paths', $output);
        $this->assertStringContainsString('deploy:copy_dirs', $output);
        $this->assertStringContainsString('deploy:lock', $output);
        $this->assertStringContainsString('deploy:prepare', $output);
        $this->assertStringContainsString('deploy:release', $output);
        $this->assertStringContainsString('deploy:shared', $output);
        $this->assertStringContainsString('deploy:symlink', $output);
        $this->assertStringContainsString('deploy:unlock', $output);
        $this->assertStringContainsString('deploy:update_code', $output);
        $this->assertStringContainsString('deploy:vendors', $output);
        $this->assertStringContainsString('deploy:writable', $output);
    }
}
