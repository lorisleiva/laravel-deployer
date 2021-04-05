<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';
require 'contrib/npm.php';

set('application', 'mylaravelapp');
set('repository', 'git@github.com:lorisleiva/mylaravelapp.git');
set('php_fpm_version', '8.0');

host('prod')
    ->set('remote_user', 'loris')
    ->set('hostname', 'mylaravelapp.com')
    ->set('deploy_path', '~/{{application}}');

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'npm:install',
    'npm:run:prod',
    'deploy:publish',
    'php-fpm:reload',
]);

task('npm:run:prod', function () {
    cd('{{release_or_current_path}}');
    run('npm run prod');
});

after('deploy:failed', 'deploy:unlock');
