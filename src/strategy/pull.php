<?php

namespace Deployer;

desc('Quick Git Pull Strategy (no zero-downtime)');
task('strategy:pull', [
    'hook:start',
    'deploy:prepare',
    'deploy:lock',
    'pull:update_code',
    'hook:ready',
    'deploy:unlock',
    'hook:done',
]);

/**
 * Strategy specific tasks
 */

desc('Git pull in the current directory');
task('pull:update_code', function () {
    if (test("[ ! -d {{deploy_path}}/current ]")) {
        throw new \Exception("There is no `current` folder to execute a `git pull` from.\nPlease choose another strategy to deploy your application before using the `pull` strategy");
    }

    run('cd {{deploy_path}}/current && git pull');
});
