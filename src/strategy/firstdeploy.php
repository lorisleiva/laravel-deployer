<?php

namespace Deployer;

desc('First deploy Strategy');
task('strategy:firstdeploy', [
    'hook:start',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'firstdeploy:shared',
    'deploy:shared',
    'deploy:vendors',
    'hook:build',
    'deploy:writable',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'firstdeploy:cleanup',
    'hook:done',
]);