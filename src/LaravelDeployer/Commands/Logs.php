<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class Logs extends BaseCommand
{
    protected $signature = 'logs {stage? : Stage or hostname}';
    protected $description = 'Dump the remote logs of your application';

    public function handle()
    {
        return $this->dep('logs');
    }
}