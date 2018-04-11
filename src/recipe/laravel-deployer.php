<?php

namespace Deployer;

/*
|--------------------------------------------------------------------------
| Official laravel recipe
|--------------------------------------------------------------------------
|
| Provides various artisan commands as deployer tasks starting with the
| `artisan` namespace, e.g. `artisan:down` to run `php artisan down`,
| and sets up default options like `shared_files` or `shared_dirs`.
|
*/

require 'recipe/laravel.php';

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

require 'task/firstdeploy.php';
require 'task/fpm.php';
require 'task/hook.php';
require 'task/horizon.php';
require 'task/local.php';
require 'task/npm.php';


/*
|--------------------------------------------------------------------------
| Available strategies
|--------------------------------------------------------------------------
|
| TODO: Document strategies.
|
*/

require 'strategy/basic.php';
require 'strategy/local.php';

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
    $strategy = 'strategy:' . get('strategy', 'basic');
    
    if (! task($strategy)) {
        writeln('<error>Strategy not found</error>');
        return invoke('deploy:fail');
    }
    
    invoke('deploy:info');
    invoke($strategy);
    invoke('success');
});

fail('deploy', 'deploy:failed');
after('deploy:failed', 'deploy:unlock');