<?php

namespace Deployer;

desc('Local Strategy');
task('strategy:local', [
    'hook:start',
    'local:build',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'local:upload',
    'deploy:shared',
    'firstdeploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'local:cleanup',
    'hook:done',
]);