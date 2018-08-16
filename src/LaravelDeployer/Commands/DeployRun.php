<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class DeployRun extends BaseCommand
{
    protected $signature = 'deploy:run {task : The task to execute} {stage? : Stage or hostname}';
    protected $description = 'Execute a given task on your hosts';

    public function handle()
    {
        // Task will be executed as a direct command of `dep`.
        return $this->dep('');
    }
}