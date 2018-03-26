<?php

namespace Deployer;

require 'recipe/laravel-deployer.php';

set('lumen', true);

desc('Deploy your Lumen application');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:failed',
    'deploy:release',
    'deploy:update_code',
    'deploy:vendors',
    'deploy:shared',
    'firstdeploy:shared',
    'hook:build',
    'deploy:writable',
    'artisan:cache:clear',
    'artisan:optimize',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'hook:done',
    'success',
]);

desc('Deploy your Lumen application with local build');
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
    'artisan:cache:clear',
    'artisan:optimize',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'local:cleanup',
    'hook:done',
    'success',
]);