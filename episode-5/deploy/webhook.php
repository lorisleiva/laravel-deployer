<?php

namespace Deployer;

use Deployer\Utility\Httpie;

set('webhook_url', function () {
    // We make this option required by throwing an exception if it's not overriden.
    throw new \RuntimeException('Please set up the "webhook_url" option.');
});

set('webhook_data', function () {
    return [
        // We can get the release number via the "release_name" option.
        'release' => get('release_name'),
        'application' => get('application'),
    ];
});

desc('Notify via webhook that the deployment started.');
task('webhook:started', sendWebhook('started'));

desc('Notify via webhook that the deployment was successful.');
task('webhook:successful', sendWebhook('successful'));

desc('Notify via webhook that the deployment failed.');
task('webhook:failed', sendWebhook('failed'));

function sendWebhook(string $state) {
    return function () use ($state) {
        Httpie::post(get('webhook_url'))
            ->body(get('webhook_data', []))
            ->header("X-Deployment-State: $state")
            ->send();
    };
};
