# 🚀 Laravel Deployer

[![Actions Status](https://github.com/lorisleiva/laravel-deployer/workflows/Tests/badge.svg)](https://github.com/lorisleiva/laravel-deployer/actions)

Laravel Deployer is a lightweight wrapper of [Deployer.org](https://github.com/deployphp/deployer) giving Artisan the power of zero-downtime deployment.

![Console showing php artisan deploy](https://user-images.githubusercontent.com/3642397/38672390-50ad0194-3e4e-11e8-93c2-d28de8659117.png)

<p align="center">
  <a href="docs/README.md"><img src="https://user-images.githubusercontent.com/3642397/38672391-50caf9e2-3e4e-11e8-862f-465d55e7e8d9.png" alt="Documentation button" height="50"></a>   
  <a href="https://www.youtube.com/playlist?list=PLP7iaQb3O2XsexM_5HMrcKNCu0IOcxIDh"><img src="https://user-images.githubusercontent.com/3642397/39360000-a2f5d668-4a1c-11e8-8869-fa7fa027fe96.png" alt="Video tutorials button" height="50"></a>
</p>

## ✨ Features
* **Simple** setup process
* **Zero downtime** deployments
* Ready-to-use tasks for Laravel
* Choose your **deployment strategy**
* Agentless, it's just SSH
* Something went wrong? **Rollback** to the previous release

## 1️⃣ Installation

```bash
composer require lorisleiva/laravel-deployer
```

## 2️⃣ Configuration
In order to generate your deployment configuration file, simply run:

```bash
php artisan deploy:init
```

* It will ask you a few questions to help you get started and generate a `config/deploy.php` file.
* Read more about the available options, tasks, strategies; about how to customize your hosts, your deployment flow and much more in the [documentation](docs/README.md).

## 3️⃣ Deployment
When you’re ready to deploy, run:

```bash
php artisan deploy
```

* If anything goes wrong during the deployment flow, the release will be discarded just like nothing happened.
* Because we are using zero-downtime deployments, make sure your server’s root path points to the `{{deploy_path}}/current` symlink.
* If your project has already been deployed, before using Laravel Deployer, you might be interested in this [first deployment strategy](docs/strategy-first-deploy.md).

## 📜 Available commands

```bash
deploy                # Deploy your application
deploy:configs        # Print host configuration
deploy:current        # Show current paths
deploy:dump <task>    # Display the task-tree for a given task
deploy:hosts          # Print all hosts
deploy:init           # Generate a deploy.php configuration file
deploy:list           # Lists available tasks
deploy:rollback       # Rollback to previous release
deploy:run <task>     # Execute a given task on your hosts
logs                  # Dump the remote logs of your application
ssh                   # Connect to host through ssh
```
