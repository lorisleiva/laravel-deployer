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

require 'task/helpers.php';

require 'task/firstdeploy.php';
require 'task/fpm.php';
require 'task/hook.php';
require 'task/horizon.php';
require 'task/local.php';
require 'task/npm.php';

/*
|--------------------------------------------------------------------------
| Main deploy task
|--------------------------------------------------------------------------
|
| This define the `deploy` task, responsible for the entire deployment flow
| of your application. You can either overidde it by copying and editing 
| this task in your deploy.php file or by using hooks to alter it.
|
*/

desc('Deploy your Laravel application');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'hook:build',
    'deploy:shared',
    'firstdeploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:clear',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:optimize',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'hook:done',
    'success',
]);

/*
|--------------------------------------------------------------------------
| Locally-built deploy task
|--------------------------------------------------------------------------
|
| This define the `deploy:local` task, responsible for deploying your app
| without bothering your hosts with asset building. This task will build 
| your release locally and upload it to your server when it's ready.
|
*/

desc('Deploy your Laravel application with local build');
task('deploy:local', [
    'deploy:info',
    'local:build',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'local:upload',
    'deploy:shared',
    'firstdeploy:shared',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:clear',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:optimize',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'local:cleanup',
    'hook:done',
    'success',
]);

after('deploy:failed', 'deploy:unlock');