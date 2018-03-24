<?php

namespace Deployer;

set('local_deploy_path', __DIR__ . '/../../.build');
set('local_upload_options', [
    'options' => [ '--exclude=node_modules' ],
]);

desc('Build your application locally');
task('local:build', function() {
    set('deploy_path', get('local_deploy_path'));
    invoke('deploy:prepare');
    invoke('deploy:release');
    invoke('deploy:update_code');
    invoke('hook:build');
    invoke('deploy:vendors');
    invoke('deploy:symlink');
})->local();

desc('Upload your locally-built application to your hosts');
task('local:upload', function () {
    upload(
        '{{local_deploy_path}}/current/',   // From local path
        '{{release_path}}',                 // To server release
        get('local_upload_options')         // With upload options
    );
});

desc('Remove locally-built application');
task('local:cleanup', function () {
    run('rm -rf {{local_deploy_path}}');
})->local();