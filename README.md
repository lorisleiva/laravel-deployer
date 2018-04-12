# 🚀 Laravel Deployer
Laravel Deployer is a lightweight wrapper of [Deployer.org](https://github.com/deployphp/deployer) giving Artisan the power of zero-downtime deployment.

![Console showing php artisan deploy](https://user-images.githubusercontent.com/3642397/38671770-8e320b2e-3e4c-11e8-8fa1-21a0f2b2e4c2.png)

<p align="center">
  <a href="docs/README.md"><img src="https://user-images.githubusercontent.com/3642397/38671022-9c1a33c6-3e4a-11e8-87e6-60ce2739d6af.png" alt="Documentation button" height="50"></a>   
  <a href="http://lorisleiva.com/zero-downtime-deployment"><img src="https://user-images.githubusercontent.com/3642397/38671021-9bf5be88-3e4a-11e8-9440-a121190992ac.png" alt="Blog article button" height="50"></a>
</p>

## ✨ Features
* **Simple** setup process and a minimal learning curve
* Ready to use recipes for **Laravel**
* **Locally built** deployment strategy
* Something went wrong? **Rollback** to the previous release
* **Agentless**, it's just SSH
* **Zero downtime** deployments

## 1️⃣ Installation

```bash
composer require lorisleiva/laravel-deployer
```

As you know, from Laravel 5.5 it will automatically discover the package. Before that register it manually.

```php
Lorisleiva\LaravelDeployer\LaravelDeployerServiceProvider::class
```

## 2️⃣ Configuration
In order to generate your deployment configuration file, simply run:

```bash
php artisan deploy:init
```

It will ask you a few questions to help you get started and generate a `deploy.php` file at the root of your project.

Read more about the available options, tasks, recipes; about how to customize your hosts, your deployment flow; about the gotchas of deploying an app that is already live and much more in the [documentation](docs).

## 3️⃣ Deployment
When you’re ready to deploy, run:

```bash
php artisan deploy
```

Or if you'd rather use a deployment strategy that [builds your assets locally](docs/how-to-deploy-local.md), run:

```bash
php artisan deploy:local
```

If anything goes wrong during the deployment flow, the release will be discarded just like nothing happened.

Because we are using zero-downtime deployments, make sure your server’s root path point to `{{deploy_path}}/current`.

If your project has already been deployed, before using Laravel Deployer, be sure to [read this](docs/first-deploy.md).

## 📜 Available commands

```bash
deploy                # Deploy your application
deploy:configs        # Print host configuration
deploy:current        # Show current paths
deploy:dump <task>    # Display the task-tree for a given task
deploy:hosts          # Print all hosts
deploy:init           # Generate deploy.php configuration file
deploy:list           # Lists available tasks
deploy:local          # Deploy your application with local build
deploy:rollback       # Rollback to previous release
deploy:run <task>     # Execute a given task on your hosts
ssh                   # Connect to host through ssh
```
