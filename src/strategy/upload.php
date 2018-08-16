<?php

namespace Deployer;

desc('Upload Strategy');
task('strategy:upload', [
    'hook:start',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'upload',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'hook:done',
]);

/** 
 * Strategy specific options
 */

set('upload_path', __DIR__ . '/../../../../..');

set('upload_vendors', false);

set('upload_options', function () { 
    $options = [
        '--exclude=.git',
        '--exclude=node_modules',
    ];

    if (! get('upload_vendors')) {
        $options[] = '--exclude=/vendor';
    }

    return compact('options'); 
});

/** 
 * Strategy specific tasks
 */

desc('Upload a given folder to your hosts');
task('upload', function () {
    $configs = array_merge_recursive(get('upload_options'), [
        'options' => ['--delete']
    ]);

    upload('{{upload_path}}/', '{{release_path}}', $configs);
});