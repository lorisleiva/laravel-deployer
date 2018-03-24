<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class DeployLocal extends BaseCommand
{
    protected $signature = 'deploy:local {stage? : Stage or hostname}';
    protected $description = 'Deploy your application with local build';

    public function handle()
    {
        $this->dep('deploy:local');
    }
}