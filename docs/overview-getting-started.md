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

## Default deployment strategy

```php
    /*
    |--------------------------------------------------------------------------
    | Default deployment strategy
    |--------------------------------------------------------------------------
    |
    | This option defines which deployment strategy to use by default on all
    | of your hosts. Laravel Deployer provides some strategies out-of-box
    | for you to choose from explained in detail in the documentation.
    |
    | Supported: 'basic', 'firstdeploy', 'local', 'pull'.
    |
    */

    'default' => 'basic',
```

* [Choose the right strategy](overview-strategy-choose.md)

## Custom deployment strategies

```php
    /*
    |--------------------------------------------------------------------------
    | Custom deployment strategies
    |--------------------------------------------------------------------------
    |
    | Here, you can easily set up new custom strategies as a list of tasks.
    | Any key of this array are supported in the `default` option above.
    | Any key matching Laravel Deployer's strategies overrides them.
    |
    */

    'strategies' => [
        'my_local_strategy' => [
            // ...
        ],
        'my_docker_strategy' => [
            // ...
        ],
    ],
```

* [Create your own strategy](overview-strategy-create.md)

## Hooks

```php
    /*
    |--------------------------------------------------------------------------
    | Hooks
    |--------------------------------------------------------------------------
    |
    | Hooks let you customize your deployments conveniently by pushing tasks 
    | into strategic places of your deployment flow. Each of the official
    | strategies invoke hooks in different ways to implement their logic.
    |
    */
   
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
            'artisan:config:cache',
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

        // After a deployment has been rolled back.
        'rollback' => [
            'fpm:reload',
        ],
    ],
```

* For example, the `local` strategy calls the `build` hook in a task that is ran locally whereas the `basic` strategy calls the `build` hook directly on the host.
* [How to create your own tasks and recipes?](how-to-custom-recipes.md)
* [Available tasks](all-tasks.md)

## Deployment options

```php
    /*
    |--------------------------------------------------------------------------
    | Deployment options
    |--------------------------------------------------------------------------
    |
    | Options follow a simple key/value structure and are used within tasks
    | to make them more configurable and reusable. You can use options to
    | configure existing tasks or to use within your own custom tasks.
    |
    */
   
    'options' => [
        // The name of your application
        'application' => env('APP_NAME', 'Laravel'),
        
        // The repository of your application
        'repository' => 'git@gitlab.com:vendor/repository.git',
    ],
```

* Run `php artisan deploy:configs` to see all options and their value.
* [Available options and defaults](all-options.md)

## Hosts

```php
    /*
    |--------------------------------------------------------------------------
    | Hosts
    |--------------------------------------------------------------------------
    |
    | Here, you can define any domain or subdomain you want to deploy to.
    | You can provide them with roles and stages to filter them during
    | deployment. Read more about how to configure them in the docs.
    |
    */
   
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

* [Configure your hosts](overview-configure-hosts.md)

## Localhost

```php
    /*
    |--------------------------------------------------------------------------
    | Localhost
    |--------------------------------------------------------------------------
    |
    | This localhost option give you the ability to deploy directly on your
    | local machine, without needing any SSH connection. You can use the
    | same configurations used by hosts to configure your localhost.
    |
    */

    'localhost' => [
        'deploy_path' => '~/Projects/Acme/build',
        // ...
    ],
```

## Include additional Deployer recipes

```php
    /*
    |--------------------------------------------------------------------------
    | Include additional Deployer recipes
    |--------------------------------------------------------------------------
    |
    | Here, you can add any third party recipes to provide additional tasks, 
    | options and strategies. Therefore, it also allows you to create and
    | include your own recipes to define more complex deployment flows.
    |
    */

    'include' => [
        'recipe/slack.php',
        'recipe/my_recipe.php',
    ],
```

* [Available recipes](all-recipes.md)
* [How to create your own tasks and recipes?](how-to-custom-recipes.md)
* [How to create complex strategies?](how-to-complex-strategies.md)