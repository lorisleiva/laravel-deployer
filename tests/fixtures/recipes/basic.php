<?php

namespace Deployer;

require 'recipe/laravel-deployer.php';

set('http_user', getenv('USER'));
set('repository', __DIR__ . '/../repository');
set('branch', null);
set('debug', true);

localhost()
    ->set('deploy_path', __DIR__ . '/../tmp');