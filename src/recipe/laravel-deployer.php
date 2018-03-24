<?php

namespace Deployer;

/*
|--------------------------------------------------------------------------
| Official laravel recipe
|--------------------------------------------------------------------------
|
| Provides various artisan commands as deployer tasks starting with the
| `artisan` namespace, e.g. `artisan:down` to run `php artisan down`.
| See full list of available tasks and their descriptions here:
|
| https://github/lorisleiva/laravel-deployer/docs/tasks.md#laravel-recipe
|
*/

require 'recipe/laravel.php';

/*
|--------------------------------------------------------------------------
| Additional tasks provided by Laravel Deployer
|--------------------------------------------------------------------------
|
| Provides useful additional tasks missing from the core laravel recipe
| like first deploy cleanups, horizon commands or fpm reloading.
|
*/

require 'task/helpers.php';

require 'task/firstdeploy.php';
require 'task/fpm.php';
require 'task/hook.php';
require 'task/horizon.php';
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
]);

after('deploy:failed', 'deploy:unlock');
after('deploy', 'success');