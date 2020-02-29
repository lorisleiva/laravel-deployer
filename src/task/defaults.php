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

set('strategy', 'basic');
set('deploy_start_time', null);
set('lumen', false);

set('shared_dirs', ['storage']);
set('shared_files', ['.env']);

set('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

set('laravel_version', function () {
    $result = run('{{bin/php}} {{release_path}}/artisan --version');
    preg_match_all('/(\d+\.?)+/', $result, $matches);
    $version = $matches[0][0] ?? 5.5;
    return $version;
});
