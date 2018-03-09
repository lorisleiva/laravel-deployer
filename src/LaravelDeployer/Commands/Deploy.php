<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class Deploy extends BaseCommand
{
    protected $signature = 'deploy {stage? : Stage or hostname}';
    protected $description = 'Deploy your Laravel application';

    public function handle()
    {
        $this->dep('deploy');
    }
}