<?php

namespace Deployer;

/*
|--------------------------------------------------------------------------
| Standard deployment strategy
|--------------------------------------------------------------------------
|
| This task defines the basic strategy, TODO: document basic strategy.
|
*/

desc('Basic Strategy');
task('strategy:basic', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'firstdeploy:shared',
    'deploy:vendors',
    'hook:build',
    'deploy:writable',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'hook:done',
]);