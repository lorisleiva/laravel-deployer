<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class DeployConfigs extends BaseCommand
{
    protected $signature = 'deploy:configs {stage? : Stage or hostname}';
    protected $description = 'Print host configuration';

    public function handle()
    {
        return $this->dep('config:dump');
    }
}