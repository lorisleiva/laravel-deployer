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