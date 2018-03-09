<?php

namespace Deployer;

require 'recipe/npm.php';

desc('Execute npm run development');
task('npm:development', 'npm run development');

desc('Execute npm run production');
task('npm:production', 'npm run production');