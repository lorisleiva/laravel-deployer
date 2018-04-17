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
    
    protected $depBinary;
    protected $useDeployerOptions = true;

    public function __construct()
    {
        $this->depBinary = base_path('vendor/bin/dep');

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
        $parameters = $this->parseParameters();
        $deployFile = $this->getDeployFile($parameters);

        if (! $deployFile) {
            $this->error("config/deploy.php file not found.");
            $this->error("Please run `php artisan deploy:init` to get started.");
            return;
        }

        $basePath = base_path();
        $parameters = $this->parseParametersAsString($parameters);
        $this->process("cd $basePath && $this->depBinary --file='$deployFile' $command $parameters");
    }

    public function getDeployFile($parameters)
    {
        if ($parameters->has('--file')) {
            $deployFile = $parameters->get('--file');
            $parameters->forget('--file');
            return $deployFile;
        }

        if ($customDeployFile = $this->getCustomDeployFile()) {
            return $customDeployFile;
        }

        if (! $configFile = $this->getConfigFile()) {
            return;
        }

        $deployFile = $configFile->toDeployFile();

        if ($parameters->has('--strategy')) {
            $deployFile->updateStrategy($parameters->get('--strategy'));
            $parameters->forget('--strategy');
        }

        return  $deployFile->store();
    }

    public function getConfigFile()
    {
        if (! file_exists(base_path('config/deploy.php'))) {
            return null;
        }

        return new ConfigFile(
            config('deploy') ?? include base_path('config/deploy.php')
        );
    }
    
    public function getCustomDeployFile()
    {
        if (! $configFile = $this->getConfigFile()) {
            return file_exists(base_path('deploy.php')) ? base_path('deploy.php') : null;
        }

        $customDeployFile = array_get($configFile->toArray(), 'custom_deployer_file');

        if (is_string($customDeployFile)) {
            return file_exists(base_path($customDeployFile)) ? base_path($customDeployFile) : null;
        }
    }

    public function process($command)
    {
        $process = new Process($command);

        try {
            dd(is_writable('/dev/tty'));
            $process->setTty(false); // TODO: true when available
        } catch (RuntimeException $e) {
            $this->output->writeln('Warning: '.$e->getMessage());
        }

        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->mustRun(function($type, $buffer) {
            $this->output->write($buffer);
        });
    }
}