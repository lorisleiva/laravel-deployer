<?php

namespace Deployer;

use Lorisleiva\LaravelDeployer\Test\DeploymentTestCase;

require 'recipe/lumen-deployer.php';

set('http_user', getenv('USER'));
set('repository', DeploymentTestCase::REPOSITORY);
set('branch', null);
set('debug', true);

localhost()
    ->set('deploy_path', DeploymentTestCase::SERVER);