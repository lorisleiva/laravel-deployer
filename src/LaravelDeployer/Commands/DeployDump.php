<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class DeployDump extends BaseCommand
{
    protected $signature = 'deploy:dump {task : Task to display the tree for}';
    protected $description = 'Display the task-tree for a given task';

    public function handle()
    {
        return $this->dep('debug:task');
    }
}