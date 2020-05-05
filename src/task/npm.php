<?php

namespace Deployer;

require 'recipe/npm.php';

desc('Execute npm run development');
task('npm:development', '{{bin/npm}} run development');

desc('Execute npm run production');
task('npm:production', '{{bin/npm}} run production');
