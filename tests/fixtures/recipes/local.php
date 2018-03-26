<?php

namespace Deployer;

require 'fixtures/recipes/basic.php';

after('hook:done', 'local:cleanup');