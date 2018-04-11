<?php

namespace Deployer;

/*
|--------------------------------------------------------------------------
| Locally-built deployment strategy
|--------------------------------------------------------------------------
|
| This task defines the local strategy, responsible for deploying your app
| without bothering your host with asset building. This task will build
| your release locally and upload it to your server when it's ready.
|
*/

desc('Local Strategy');
task('strategy:local', [
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