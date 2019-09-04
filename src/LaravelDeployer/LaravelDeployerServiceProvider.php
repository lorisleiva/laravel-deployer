<?php 

namespace Lorisleiva\LaravelDeployer;

use Illuminate\Support\ServiceProvider;

class LaravelDeployerServiceProvider extends ServiceProvider
{
    protected $commands = [
        Commands\Deploy::class,
        Commands\DeployConfigs::class,
        Commands\DeployCurrent::class,
        Commands\DeployDump::class,
        Commands\DeployHosts::class,
        Commands\DeployInit::class,
        Commands\DeployList::class,
        Commands\DeployRollback::class,
        Commands\DeployRun::class,
        Commands\Logs::class,
        Commands\Ssh::class,
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }
}
