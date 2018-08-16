<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class Ssh extends BaseCommand
{
    protected $signature = 'ssh {hostname? : Host to connect to}';
    protected $description = 'Connect to host through ssh';

    public function handle()
    {
        return $this->dep('ssh');
    }
}