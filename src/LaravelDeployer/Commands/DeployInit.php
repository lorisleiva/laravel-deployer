<?php

namespace Lorisleiva\LaravelDeployer\Commands;

use Lorisleiva\LaravelDeployer\ConfigFileGenerator;

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

    public function __construct(ConfigFileGenerator $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    public function handle()
    {
        // dd(app()->version());
        $this->configureGenerator();
        $this->generator->generate();
    }

    public function configureGenerator()
    {
        if ($this->option('all')) {
            return $this->allOptions();
        }

        $this->welcomeMessage('🚀',  'Let\'s configure your deployment!');
        $this->defineRepositoryUrl();
        $this->defineHostname();
        $this->defineForge();
        $this->defineDeployementPath();
        $this->defineAdditionalHooks();
    }

    public function allOptions()
    {
        if ($hostname = $this->argument('hostname')) {
            $this->generator->setHost('name', $hostname);
        }

        if ($this->option('forge')) {
            $this->generator->useForge();
        }

        $this->generator->add('hooks.build', 'npm:install');
        $this->generator->add('hooks.build', 'npm:production');
        $this->generator->add('hooks.ready', 'artisan:migrate');
        $this->generator->add('hooks.ready', 'artisan:horizon:terminate');
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

    public function defineRepositoryUrl()
    {
        $repository = $this->ask(
            'Repository URL', 
            $this->generator->get('options.repository')
        );

        $this->generator->set('options.repository', $repository);
    }

    public function defineHostname()
    {
        if (! $hostname = $this->argument('hostname')) {
            $hostname = $this->ask(
                'Hostname of your deployment server', 
                $this->generator->getHostname()
            );
        }

        $this->generator->setHost('name', $hostname);
    }

    public function defineForge()
    {
        $question = 'Do you use Laravel Forge to maintain your server?';
        if ($this->option('forge') || $this->confirm($question)) {
            return $this->generator->useForge();
        }
    }

    public function defineDeployementPath()
    {
        $path = $this->ask(
            'Deployment path (absolute to the server)', 
            $this->generator->getHost('deploy_path')
        );

        $this->generator->setHost('deploy_path', $path);
    }

    public function defineAdditionalHooks()
    {
        $npm = $this->choice(
            'Do you want to compile your asset during deployment?', 
            ['No', 'Yes using `npm run production`', 'Yes using `npm run development`'], 1
        );

        if ($npm !== 'No') {
            $build = $npm === 'Yes using `npm run production`' ? 'production' : 'development';
            $this->generator->add('hooks.build', 'npm:install');
            $this->generator->add('hooks.build', "npm:$build");
        }
        
        if ($this->confirm('Do you want to migrate during deployment?', true)) {
            $this->generator->add('hooks.ready', 'artisan:migrate');
        }
        
        if ($this->confirm('Do you want to terminate horizon after each deployment?')) {
            $this->generator->add('hooks.ready', 'artisan:horizon:terminate');
        }
    }
}