<?php

namespace Lorisleiva\LaravelDeployer\Commands;

use Illuminate\Console\Command;
use Lorisleiva\LaravelDeployer\ConfigFile;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;

class BaseCommand extends Command
{
    protected $depBinary;
    protected $useDeployerOptions = true;

    public function __construct()
    {
        $this->depBinary = base_path('vendor/bin/dep');

        $deployerOptions = "
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
        $configFile = $this->getConfigFile();
        $customDeployFile = $this->getCustomDeployFile();

        if (! $configFile && ! $customDeployFile) {
            $this->error("config/deploy.php file not found.");
            $this->error("Please run `php artisan deploy:init` to get started.");
            return;
        }

        $deployFile = $customDeployFile ?? $configFile->toDeployFile()->store();
        $parameters = $this->parseParameters($deployFile);
        $basePath = base_path();
        $this->process("cd $basePath && $this->depBinary $command $parameters");
    }

    public function parseParameters($deployFile)
    {
        $parameters = $this->parseArguments()->merge($this->parseOptions());

        if ($this->getOutput()->isDebug()) {
            $parameters->push('-vvv');
        } elseif ($this->getOutput()->isVeryVerbose()) {
            $parameters->push('-vv');
        } elseif ($this->getOutput()->isVerbose()) {
            $parameters->push('-v');
        } elseif ($this->getOutput()->isQuiet()) {
            $parameters->push('-q');
        }

        if (! $parameters->has('--file')) {
            $parameters->put('--file', $deployFile);
        }

        return (string) new ArrayInput($parameters->toArray(), null);
    }

    public function parseArguments()
    {
        return collect($this->arguments())
            ->reject(function ($value) {
                return ! $value && ! is_string($value) && ! is_numeric($value);
            })
            ->pipe(function ($arguments) {
                $command = $arguments->get('command');
                return $command && $arguments->get(0) === $command
                    ? $arguments->forget(0)
                    : $arguments;
            })
            ->forget('command');
    }

    public function parseOptions()
    {
        $i = 0;
        return collect($this->options())
            ->reject(function ($value) {
                return ! $value && ! is_string($value) && ! is_numeric($value);
            })
            ->mapWithKeys(function ($value, $key) use (&$i) {
                return is_bool($value) ? [ $i++ => "--$key" ] : [ "--$key" => $value ];
            })
            ->pipe(function ($options) {
                return ! $options->contains('--no-ansi')
                    ? $options->push('--ansi')
                    : $options;
            });
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
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run(function($type, $buffer) {
            $this->output->write($buffer);
        });
    }
}