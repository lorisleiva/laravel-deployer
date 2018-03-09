<?php

namespace Deployer;

desc('Execute artisan horizon:terminate');
task('artisan:horizon:terminate', function () {
    $output = run('{{bin/php}} {{release_path}}/artisan horizon:terminate');
    writeln('<info>' . $output . '</info>');
});