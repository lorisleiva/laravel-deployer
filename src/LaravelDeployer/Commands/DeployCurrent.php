<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class DeployCurrent extends BaseCommand
{
    protected $signature = 'deploy:current {stage? : Stage or hostname}';
    protected $description = 'Show current paths';

    public function handle()
    {
        $this->dep('config:current');
    }
}
