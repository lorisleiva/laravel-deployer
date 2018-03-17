<?php

namespace Deployer;

desc('Execute artisan horizon:terminate');
task('artisan:horizon:terminate', artisan('horizon:terminate', true));