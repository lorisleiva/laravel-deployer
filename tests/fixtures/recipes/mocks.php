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

// Mock vendors
task('deploy:vendors', function() {});

// Mock artisan commands
task('artisan:storage:link', function() {});
task('artisan:view:clear', function() {});
task('artisan:cache:clear', function() {});
task('artisan:config:cache', function() {});
task('artisan:optimize', function() {});

// Mock public info task
task('deploy:info_debug', ['deploy:info']);