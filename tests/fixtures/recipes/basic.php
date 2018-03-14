<?php

namespace Deployer;

require 'recipe/laravel-deployer.php';

set('http_user', getenv('USER'));
set('repository', __DIR__ . '/../repository');
set('branch', null);
set('debug', true);

localhost()
    ->set('deploy_path', __DIR__ . '/../tmp');

// Mock composer install
task('deploy:vendor', function() {
    $vendorFolder = realpath(__DIR__ . '../../../vendor');
    run("ln -s $vendorFolder {{release_path}}/vendor");
});