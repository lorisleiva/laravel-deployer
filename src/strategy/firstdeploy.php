<?php

namespace Deployer;

desc('First Deploy Strategy');
task('strategy:firstdeploy', [
    'hook:start',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'firstdeploy:shared',
    'deploy:shared',
    'deploy:vendors',
    'hook:build',
    'deploy:writable',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'firstdeploy:cleanup',
    'hook:done',
]);

/**
 * Strategy specific tasks
 */

desc('Copying the shared folder from the deploy path if possible');
task('firstdeploy:shared', function () {
    copyShared('{{deploy_path}}', '{{release_path}}');
});

desc('Deletes everything from deploy path that isn\'t from deployer');
task('firstdeploy:cleanup', function () {
    $filesToKeep = '\.dep\|current\|release\|releases\|shared';
    $filesToDelete = run("echo `ls -A {{deploy_path}} | grep -v '^$filesToKeep$'`");

    writeln('');
    writeln('|' . str_repeat('-', 56));
    writeln('| <fg=yellow;options=bold>[WARNING] You are about to delete some files</>');
    writeln('|' .str_repeat('-', 56));
    writeln('|');
    writeln('| You are about to delete all files and folders from your');
    writeln('| deployment path that are not deployer folders, that is:');
    writeln('| > `.dep`, `current`, `release`, `releases` and `shared`');
    writeln('| Make sure your server points to the "/current" symlink.');
    writeln('');
    writeln("<info>Deleting:</info> $filesToDelete");
    writeln('<info>From directory:</info> {{deploy_path}}');
    writeln('');

    $question = "Are you sure you want to continue and delete those elements?";
    if (get('debug', false) || askConfirmation($question, false)) {
        run("cd {{deploy_path}} && rm -rf $filesToDelete");
    }
});
