<?php

namespace Lorisleiva\LaravelDeployer\Commands;

use Lorisleiva\LaravelDeployer\ConfigFileBuilder;

class DeployInit extends BaseCommand
{
    protected $builder;

    protected $signature = "deploy:init
        {hostname? : The hostname of the deployment server}
        {--f|forge : Whether the server is maintained by Laravel Forge}
        {--a|all : Generate configuration with all possible options}
    ";
    
    protected $useDeployerOptions = false;
    protected $description = 'Generate a deploy.php configuration file';

    public function __construct(ConfigFileBuilder $builder)
    {
        parent::__construct();
        $this->builder = $builder;
    }

    public function handle()
    {
        if ($this->configFileExists()) {
            return;
        }
        
        $this->configureBuilder();
        $this->builder->build()->store();
    }

    public function configFileExists()
    {
        $filepath = base_path('config' . DIRECTORY_SEPARATOR . 'deploy.php');

        return file_exists($filepath)
            && ! $this->confirm("<fg=red;options=bold>A configuration file already exists.</>\nAre you sure you want to continue and override it?");
    }

    public function configureBuilder()
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
            $this->builder->setHost('name', $hostname);
        }

        if ($this->option('forge')) {
            $this->builder->useForge();
        } else {
            $this->builder->reloadFpm();
        }

        $this->builder->add('hooks.build', 'npm:install');
        $this->builder->add('hooks.build', 'npm:production');
        $this->builder->add('hooks.ready', 'artisan:migrate');
        $this->builder->add('hooks.ready', 'artisan:horizon:terminate');
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
            $this->builder->get('options.repository')
        );

        $this->builder->set('options.repository', $repository);
    }

    public function defineHostname()
    {
        if (! $hostname = $this->argument('hostname')) {
            $hostname = $this->ask(
                'Hostname of your deployment server', 
                $this->builder->getHostname()
            );
        }

        $this->builder->setHost('name', $hostname);
    }

    public function defineForge()
    {
        $question = 'Do you use Laravel Forge to maintain your server?';
        
        if ($this->option('forge') || $this->confirm($question)) {
            return $this->builder->useForge($this->askPhpVersion());
        }

        if($this->confirm('Do you want to reload php-fpm after each deployment?')) {
            return $this->builder->reloadFpm($this->askPhpVersion());
        };
    }

    public function askPhpVersion()
    {
        return $this->ask(
            'Which php version are you using? (format: #.#)', 
            ConfigFileBuilder::DEFAULT_PHP_VERSION
        );
    }

    public function defineDeployementPath()
    {
        $path = $this->ask(
            'Deployment path (absolute to the server)', 
            $this->builder->getHost('deploy_path')
        );

        $this->builder->setHost('deploy_path', $path);
    }

    public function defineAdditionalHooks()
    {
        $npm = $this->choice(
            'Do you want to compile your asset during deployment with npm/yarn?',
            [
                'No',
                'Yes using `npm run production`',
                'Yes using `yarn production`',
            ], 1
        );
        
        if ($npm !== 'No') {
            $manager = $npm === 'Yes using `npm run production`' ? 'npm' : 'yarn';
            $this->builder->add('hooks.build', "$manager:install");
            $this->builder->add('hooks.build', "$manager:production");
        }

        if ($this->confirm('Do you want to migrate during deployment?', true)) {
            $this->builder->add('hooks.ready', 'artisan:migrate');
        }

        if ($this->confirm('Do you want to terminate horizon after each deployment?')) {
            $this->builder->add('hooks.ready', 'artisan:horizon:terminate');
        }
    }
}
