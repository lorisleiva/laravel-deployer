<?php

namespace Deployer;

/**
 * Throws an exception if current strategy does not exist.
 */
task('ld:check_strategy', function () {
    $strategy = get('strategy');
    try {
        task("strategy:$strategy");
    } catch (\InvalidArgumentException $e) {
        throw new \InvalidArgumentException("Strategy `$strategy` not found");
    }
})
    ->shallow()
    ->setPrivate();

/**
 * Get global starting time of deployment.
 * No matter how many hosts are being deployed on.
 */
task('ld:get_start_time', function () {
    set('deploy_start_time', microtime(true));
})
    ->local()
    ->shallow()
    ->setPrivate();

/**
 * @override
 * Include total execution time in success message.
 */
task('success', function () {
    $start = get('deploy_start_time');
    $message = '🚀  <info>Successfully deployed</info>';

    if (is_null($start)) {
        return writeln($message);
    }

    $time = microtime(true) - $start;
    $unit = $time < 1 ? 'ms' : 's';
    $time = $time < 1 ? round($time * 1000) : round($time, 2);
    $timeMessage = " in $time$unit";
    writeln("$message$timeMessage");
})
    ->local()
    ->shallow()
    ->setPrivate();

/**
 * @override
 * Include strategy in information message.
 */
task('deploy:info', function () {
    $what = '';
    $branch = get('branch');
    if (!empty($branch)) {
        $what = "<fg=magenta>$branch</fg=magenta>";
    }
    if (input()->hasOption('tag') && !empty(input()->getOption('tag'))) {
        $tag = input()->getOption('tag');
        $what = "tag <fg=magenta>$tag</fg=magenta>";
    } elseif (input()->hasOption('revision') && !empty(input()->getOption('revision'))) {
        $revision = input()->getOption('revision');
        $what = "revision <fg=magenta>$revision</fg=magenta>";
    }
    if (empty($what)) {
        $what = "<fg=magenta>HEAD</fg=magenta>";
    }
    writeln("✈︎ Deploying $what on <fg=cyan>{{hostname}}</fg=cyan> with <info>{{strategy}}</info> strategy");
})
    ->shallow()
    ->setPrivate();

/**
 * @override
 * Skip composer install if we are uploading with the upload_vendors option set to true.
 */
desc('Installing vendors');
task('deploy:vendors', function () {
    if (get('strategy') === 'upload' && get('upload_vendors')) {
        return writeln("<fg=yellow;options=bold;>Warning: </><fg=yellow;>option `upload_vendors` is set to true. Skipping...</>");
    }

    if (!commandExist('unzip')) {
        writeln('<comment>To speed up composer installation setup "unzip" command with PHP zip extension https://goo.gl/sxzFcD</comment>');
    }
    run('cd {{release_path}} && {{bin/composer}} {{composer_options}}');
});
