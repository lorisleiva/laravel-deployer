<?php

namespace Lorisleiva\LaravelDeployer\Commands;

use Illuminate\Console\Command;
use Lorisleiva\LaravelDeployer\Concerns\ParsesCliParameters;
use Lorisleiva\LaravelDeployer\ConfigFile;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class BaseCommand extends Command
{
    use ParsesCliParameters;
    
    protected $providedFile;
    protected $providedStrategy;
    protected $useDeployerOptions = true;

    public function __construct()
    {
        $deployerOptions = "
            {--s|strategy= : Default deployement strategy}
            {--p|parallel : Run tasks in parallel}
            {--l|limit= : How many host to run in parallel?}
            {--no-hooks : Run task without after/before hooks}
            {--log= : Log to file}
            {--roles= : Roles to deploy}
            {--hosts= : Host to deploy, comma separated, supports ranges [:]}
            {--o|option=* : Sets configuration option}
            {--f|file= : Specify Deployer file}
            {--tag= : Tag to deploy}
            {--revision= : Revision to deploy}
            {--branch= : Branch to deploy}
        ";

        if ($this->useDeployerOptions) {
            $this->signature .= $deployerOptions;
        }
        
        parent::__construct();
    }

    public function dep($command)
    {
        $this->providedFile = $this->getParameters()->pull('--file');
        $this->providedStrategy = $this->getParameters()->pull('--strategy');

        if (! $deployFile = $this->getDeployFile()) {
            $this->error("config/deploy.php file not found.");
            $this->error("Please run `php artisan deploy:init` to get started.");
            return;
        }

        $parameters = $this->getParametersAsString();
        $this->process("vendor/bin/dep --file='$deployFile' $command $parameters");
    }

    public function getDeployFile()
    {
        if ($this->providedFile) {
            return $this->providedFile;
        }

        if ($customDeployFile = $this->getCustomDeployFile()) {
            return $customDeployFile;
        }

        if ($configFile = $this->getConfigFile()) {
            return $configFile
                ->toDeployFile()
                ->updateStrategy($this->providedStrategy)
                ->store();
        }
    }

    public function getConfigFile()
    {
        if (file_exists(base_path('config/deploy.php'))) {
            return new ConfigFile(
                config('deploy') ?? include base_path('config/deploy.php')
            );
        }
    }
    
    public function getCustomDeployFile()
    {
        if (! $configFile = $this->getConfigFile()) {
            return file_exists(base_path('deploy.php')) ? base_path('deploy.php') : null;
        }

        if (is_string($custom = $configFile->get('custom_deployer_file'))) {
            return file_exists(base_path($custom)) ? base_path($custom) : null;
        }
    }

    public function process($command)
    {
        $process = (new Process($command))
            ->setTty($this->isTtySupported())
            ->setWorkingDirectory(base_path())
            ->setTimeout(null)
            ->setIdleTimeout(null)
            ->run(function($type, $buffer) {
                $this->output->write($buffer);
            });
    }

    public function isTtySupported()
    {
        if (env('APP_ENV') === 'testing') {
            return false;
        }

        return (bool) @proc_open('echo 1 >/dev/null', [
            ['file', '/dev/tty', 'r'], 
            ['file', '/dev/tty', 'w'], 
            ['file', '/dev/tty', 'w'], 
        ], $pipes);
    }
}