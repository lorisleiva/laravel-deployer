<?php

namespace Deployer;

/**
 * Overrides from Deployer.
 */

set('allow_anonymous_stats', false);
set('git_tty', false); 

/**
 * New Laravel Deployer options.
 */

set('lumen', false);