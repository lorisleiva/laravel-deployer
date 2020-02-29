<?php

namespace Deployer;

require 'recipe/common.php';

/*
|--------------------------------------------------------------------------
| Additional tasks provided by Laravel Deployer
|--------------------------------------------------------------------------
|
| Provides useful additional tasks missing from the core laravel recipe
| like first deploy cleanups, horizon commands, fpm reloading, local
| strategy deployments, shallow hooks, npm asset building, etc.
|
*/

require 'task/defaults.php';
require 'task/helpers.php';
require 'task/common.php';

require 'task/artisan.php';
require 'task/fpm.php';
require 'task/hook.php';
require 'task/logs.php';
require 'task/npm.php';
require 'task/yarn.php';

/*
|--------------------------------------------------------------------------
| Available strategies
|--------------------------------------------------------------------------
|
| This section includes all strategies provided by Laravel Deployer by
| default. They are registered as tasks using the naming convention
| "strategy:key" where key is used within the configuration file.
|
*/

require 'strategy/basic.php';
require 'strategy/firstdeploy.php';
require 'strategy/local.php';
require 'strategy/pull.php';
require 'strategy/upload.php';

/*
|--------------------------------------------------------------------------
| Deploy task
|--------------------------------------------------------------------------
|
| This define the `deploy` task, responsible for the entire deployment flow of
| your application. You can either overidde it by copying/editing this task
| into your deploy.php file or by using strategies and hooks to alter it.
|
*/

desc('Deploy your application');
task('deploy', function() {
    invoke('ld:check_strategy');
    invoke('deploy:info');
    invoke('strategy:' . get('strategy'));
})->shallow();

// Calculate total execution time on success.
before('deploy', 'ld:get_start_time');
after('deploy', 'success');

// Unlock when deployment fails.
fail('deploy', 'deploy:failed');
after('deploy:failed', 'deploy:unlock');

// Add rollback hook.
after('rollback', 'hook:rollback');
