<?php

namespace Deployer;

// Mock npm install
task('npm:install', function() {
    run('mkdir -p {{release_path}}/node_modules/vendor/package');
});

// Mock npm development
task('npm:development', function() {
    run('echo "compiled app.css" > {{release_path}}/public/css/app.css');
    run('echo "compiled app.js" > {{release_path}}/public/js/app.js');
});

// Mock other tasks.
task('deploy:vendors', function() {});
task('fpm:reload', function() {});
task('artisan:storage:link', function() {});
task('artisan:view:clear', function() {});
task('artisan:view:cache', function() {});
task('artisan:cache:clear', function() {});
task('artisan:event:clear', function() {});
task('artisan:event:cache', function() {});
task('artisan:config:cache', function() {});
task('artisan:optimize', function() {});
task('artisan:optimize:clear', function() {});

// Mock public info task
task('deploy:info_debug', ['deploy:info']);
