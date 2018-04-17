# Getting started

Following the instructions on the README page:

```bash
# install
composer require lorisleiva/laravel-deployer

# initialize configurations
php artisan deploy:init
```

You should end up with a `deploy.php` file in your `config/` folder.

**:fire: Pro tips:**
* Runing `php artisan deploy:init your.hostname.com -a`, will set you up with everything without asking you any questions.
* Using the `-f` option will optimize the configuration file for servers that are maintained by Laravel Forge.
* You can combine the two above.

Before starting you first deployment, you should go check your `config/deploy.php` file to make sure it suits your deployment flow.

*TODO: How to deploy on my local machine? add localhost section (and all config sections in order).*

## Check the options

```php
// config/deploy.php

'options' => [
    // The name of your application
    'application' => env('APP_NAME', 'Laravel'),
    
    // The repository of your application
    'repository' => 'git@gitlab.com:vendor/repository.git',
],
```

* Run `php artisan deploy:configs` to see all options and their value.
* [Check out all available options](all-options.md)


## Check the hosts

```php
// config/deploy.php

'hosts' => [
    // Your hostname can be a domain or an IP address.
    'your.hostname.com' => [

        // The deploy path. Where the code resides in your host.
        'deploy_path' => '/var/www/html/your.hostname.com',

        // (Optional) Who should Deployer connect as during deployment. 
        'user' => 'root',

        // (Optional) The stage of your host.
        // Can be useful to distinguish a production server and a staging server.
        'stage' => 'prod',
    ],
    
    // You can set up as many hosts as you want.
],
```

* Deployer will access your server via SSH as the provided user.
* More authentication configurations can be found [here](overview-configure-hosts.md).

## Check the hooks

Hooks let you easily customize your deployment by adding tasks into strategic places of the deployment flow. Each of the various strategies call those hooks in deferent way to implement their logic. For example, the `local` strategy calls the `build` hook in a task that is ran locally whereas the `basic` strategy calls the `build` hook directly on the host.

```php
// config/deploy.php

'hooks' => [
    // Right before we start deploying.
    'start' => [],

    // Code and composer vendors are ready but nothing is built.
    'build' => [
        'npm:install',
        'npm:production',
    ],

    // Deployment is done but not live yet (before symlink)
    'ready' => [
        'artisan:storage:link',
        'artisan:view:clear',
        'artisan:cache:clear',
        'artisan:config:cache',
        'artisan:optimize',
        'artisan:migrate',
    ],

    // Deployment is done and live
    'done' => [
        'fpm:reload',
    ],

    // Deployment succeeded. Every task succeeded, including the tasks in the `done` hook.
    'success' => [],

    // Deployment failed. This can happen at any point of the deployment.
    'fail' => [],
],
```

* [Check out how to create your own tasks and recipes](how-to-custom-recipes.md).
* [Check out all available tasks](all-tasks.md).

**:fire: Pro tips:**
* Run `php artisan deploy:dump strategy:basic` (replace `basic` with the strategy you use) to display the tree of tasks that are executed during deployment including the ones added via hooks.
* Note that this command can do that for any given task `php artisan deploy:dump mycustom:task`.