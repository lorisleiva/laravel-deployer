<?php

namespace Deployer;

desc('Basic Strategy');
task('strategy:basic', [
    'hook:start',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'hook:build',
    'deploy:writable',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'hook:done',
]);