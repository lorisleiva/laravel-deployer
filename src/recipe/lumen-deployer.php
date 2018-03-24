<?php

namespace Deployer;

require 'recipe/laravel-deployer.php';

set('lumen', true);

desc('Deploy your Lumen application');
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
    'artisan:cache:clear',
    'artisan:optimize',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'hook:done',
]);

after('deploy:failed', 'deploy:unlock');
after('deploy', 'success');