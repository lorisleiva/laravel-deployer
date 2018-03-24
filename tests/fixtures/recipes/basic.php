<?php

namespace Deployer;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

require 'recipe/laravel-deployer.php';
require 'fixtures/recipes/mocks.php';

set('http_user', getenv('USER'));
set('repository', DeploymentTestCase::REPOSITORY);
set('branch', null);
set('debug', true);

localhost()
    ->set('deploy_path', DeploymentTestCase::SERVER);
    
after('hook:build', 'npm:install');
after('hook:build', 'npm:development');