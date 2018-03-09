# 🚀 Laravel Deployer
Laravel Deployer is a lightweight wrapper of [Deployer](https://github.com/deployphp/deployer) giving Artisan the power of zero-downtime deployment.

* [Documentation](docs)
* Blog article (TODO)

## Installation

```bash
composer require lorisleiva/laravel-deployer
```

As you know, from Laravel 5.5 it will automatically discover the package. Before that register it manually.

```php
Lorisleiva\LaravelDeployer\LaravelDeployerServiceProvider::class
```

## Configuration
In order to generate your deployment configuration file, simply run:

```bash
php artisan deploy:init
```

It will ask you a few questions to help you get started and generate a `deploy.php` file at the root of your project.

Read more about the available options, tasks, recipes; about how to customize your hosts, your deployment flow; about the gotchas of deploying an app that is already live and much more in the [documentation](docs).

## Deployment
When you’re ready to deploy, run:

```bash
php artisan deploy
```

If anything goes wrong during the deployment flow, the release will be discarded just like nothing happened.

Because we are using zero-downtime deployments, make sure your server’s root path point to `{{deploy_path}}/current`.

If your project has already been deployed, before using Laravel Deployer, be sure to [read this](docs/first-deploy.md).

## Available commands

```bash
deploy                # Deploy your Laravel application
deploy:configs        # Print host configuration
deploy:current        # Show current paths
deploy:dump <task>    # Display the task-tree for a given task
deploy:hosts          # Print all hosts
deploy:init           # Generate deploy.php configuration file
deploy:list           # Lists available tasks
deploy:run <task>     # Execute a given task on your hosts
ssh                   # Connect to host through ssh
```