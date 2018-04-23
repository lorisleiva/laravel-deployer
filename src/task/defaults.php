<?php

namespace Deployer;

/**
 * Overrides from Deployer.
 */

set('allow_anonymous_stats', false);

set('git_tty', function() {
    try {
        $output = run('ssh -T git@github.com');
    } catch (\Exception $e) {
        $fail = strpos($e->getMessage(), 'Host key verification failed');
        return $fail !== false;
    }
    
    return false;
});

/**
 * New Laravel Deployer options.
 */

set('strategy', 'basic');
set('deploy_start_time', null);
set('lumen', false);