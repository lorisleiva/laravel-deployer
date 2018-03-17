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
    'deploy:shared',
    'firstdeploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:cache:clear',
    'artisan:optimize',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);

after('deploy:failed', 'deploy:unlock');
after('deploy', 'success');