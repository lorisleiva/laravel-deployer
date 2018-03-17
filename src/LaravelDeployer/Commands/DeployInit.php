<?php

namespace Lorisleiva\LaravelDeployer\Commands;

use Lorisleiva\LaravelDeployer\DeployFileGenerator;

class DeployInit extends BaseCommand
{
    protected $generator;

    protected $signature = "deploy:init
        {hostname? : The hostname of the deployment server}
        {--f|forge : Whether the server is maintained by Laravel Forge}
        {--a|all : Generate configuration with all possible options}
    ";
    
    protected $useDeployerOptions = false;
    protected $description = 'Generate deploy.php configuration file';

    public function __construct(DeployFileGenerator $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    public function handle()
    {
        $this->configureGenerator();
        $this->generator->generate();
    }

    public function configureGenerator()
    {
        $this->resolveRecipe();

        if ($this->option('all')) {
            return $this->allOptions();
        }

        $this->welcomeMessage('🚀',  'Let\'s configure your deployment!');
        $this->defineApplicationName();
        $this->defineRepositoryUrl();
        $this->defineHostname();
        $this->defineForgeAndLocalhosts();
        $this->defineDeployementPath();
        $this->defineAdditionalHooks();
    }

    public function resolveRecipe()
    {
        if (preg_match('/Lumen/', app()->version())) {
            $this->generator->recipe('lumen-deployer');
        }
    }

    public function allOptions()
    {
        if ($hostname = $this->argument('hostname')) {
            $this->generator->hostname($hostname);
        }

        $this->option('forge')
            ? $this->generator->useForge()
            : $this->generator->localhost();

        $this->generator->useNpm('production');
        $this->generator->migrate();
        $this->generator->terminateHorizon();
    }

    public function welcomeMessage($emoji, $message)
    {
        if (! $this->input->isInteractive()) {
            return;
        }

        $this->output->newLine();
        $this->comment(str_repeat('*', strlen($message) + 15));
        $this->comment("*     $emoji  $message     *");
        $this->comment(str_repeat('*', strlen($message) + 15));
        $this->output->newLine();
    }

    public function defineApplicationName()
    {
        $app = $this->ask(
            'Application name', 
            $this->generator->get('application')
        );

        $this->generator->application($app);
    }

    public function defineRepositoryUrl()
    {
        $repository = $this->ask(
            'Repository URL', 
            $this->generator->get('repository')
        );

        $this->generator->repository($repository);
    }

    public function defineHostname()
    {
        if (! $hostname = $this->argument('hostname')) {
            $hostname = $this->ask('Hostname of your deployment server', 'example.com');
        }

        $this->generator->hostname($hostname);
    }

    public function defineForgeAndLocalhosts()
    {
        $question = 'Do you use Laravel Forge to maintain your server?';
        if ($this->option('forge') || $this->confirm($question)) {
            return $this->generator->useForge();
        }

        $question = 'Do you want to enable deployment directly from the server?';
        if ($this->confirm($question)) {
            return $this->generator->localhost();
        }
    }

    public function defineDeployementPath()
    {
        $path = $this->ask(
            'Deployment path (absolute to the server)', 
            $this->generator->get('host_deploy_path')
        );

        $this->generator->deploymentPath($path);
    }

    public function defineAdditionalHooks()
    {
        $npm = $this->choice(
            'Do you want to compile your asset during deployment?', 
            ['No', 'Yes using `npm run production`', 'Yes using `npm run development`'], 1
        );

        if ($npm !== 'No') {
            $this->generator->useNpm(
                $npm === 'Yes using `npm run production`' 
                    ? 'production' 
                    : 'development'
            );
        }

        if ($this->confirm('Do you want to migrate during deployment?', true)) {
            $this->generator->migrate();
        }

        if ($this->confirm('Do you want to terminate horizon after each deployment?')) {
            $this->generator->terminateHorizon();
        }
    }
}