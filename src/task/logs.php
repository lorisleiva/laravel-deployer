<?php

namespace Deployer;

set('log_lines', 200);
set('log_command', 'cat storage/logs/laravel.log | grep -Ev "^#[[:digit:]]|^\[stacktrace\]$|^\"\}$" | tail -n {{log_lines}}');

desc('Read logs from a given host');
task('logs', function() {
    writeln(run('cd {{deploy_path}}/current && {{log_command}}'));
})->shallow();
