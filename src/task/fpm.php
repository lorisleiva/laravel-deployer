<?php

namespace Deployer;

set('php_fpm_service', 'php7.1-fpm');
set('php_fpm_command', 'echo "" | sudo -S /usr/sbin/service {{php_fpm_service}} reload');

desc('Reload the php-fpm service');
task('fpm:reload', function () {
    run('{{php_fpm_command}}');
});
