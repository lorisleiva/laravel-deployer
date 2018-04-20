<?php

namespace Deployer;

set('log_command', 'cat storage/logs/laravel.log | grep -Ev "^#\d|^\[stacktrace\]$|^\"\}$"');

desc('Read logs from a given host');
task('logs', function() {
    writeln(run('cd {{deploy_path}}/current && {{log_command}}'));
})->shallow();