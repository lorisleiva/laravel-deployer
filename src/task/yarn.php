<?php

namespace Deployer;

require 'recipe/yarn.php';

desc('Execute yarn development');
task('yarn:development', '{{bin/yarn}} development');

desc('Execute yarn production');
task('yarn:production', '{{bin/yarn}} production');
