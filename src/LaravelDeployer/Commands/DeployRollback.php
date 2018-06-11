<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class DeployRollback extends BaseCommand
{
    protected $signature = 'deploy:rollback {stage? : Stage or hostname}';
    protected $description = 'Rollback to previous release';

    public function handle()
    {
        $this->dep('rollback');
    }
}
