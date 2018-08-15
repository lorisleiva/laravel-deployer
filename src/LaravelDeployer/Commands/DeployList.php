<?php

namespace Lorisleiva\LaravelDeployer\Commands;

class DeployList extends BaseCommand
{
    protected $signature = 'deploy:list
        {namespace? : The namespace name}
        {--raw : To output raw command list}
        {--format=txt : The output format (txt, xml, json, or md)}
    ';

    protected $description = 'Lists available tasks';

    public function handle()
    {
        return $this->dep('list');
    }
}