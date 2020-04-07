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

    protected $parameters;
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
        // Merge arguments and options.
        $this->parameters = $this->getParameters();
        $this->providedFile = $this->parameters->pull('--file');
        $this->providedStrategy = $this->parameters->pull('--strategy');

        // Force Ansi mode if not specified.
        if (! $this->parameters->contains('--ansi') && ! $this->parameters->contains('--no-ansi')) {
            $this->parameters->push('--ansi');
        }

        // Fetch deploy config file.
        if (! $deployFile = $this->getDeployFile()) {
            $this->error("config/deploy.php file not found.");
            $this->error("Please run `php artisan deploy:init` to get started.");
            return;
        }

        // Delegate to DeployerPHP with the right parameters.
        $parameters = $this->getParametersAsString($this->parameters);
        $depBinary = 'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'dep';
        return $this->process("$depBinary --file=$deployFile $command $parameters");
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
        $filepath = base_path('config' . DIRECTORY_SEPARATOR . 'deploy.php');

        if (file_exists($filepath)) {
            return new ConfigFile(
                config('deploy') ?? include $filepath
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

    public function process(string $command)
    {
        return Process::fromShellCommandline($command)
            ->setTty($this->isTtySupported())
            ->setWorkingDirectory(base_path())
            ->setTimeout(null)
            ->setIdleTimeout(null)
            ->mustRun(function($type, $buffer) {
                $this->output->write($buffer);
            })
            ->getExitCode();
    }

    public function isTtySupported()
    {
        return config('app.env') !== 'testing' && Process::isTtySupported();
    }
}
