# 🚀 Laravel Deployer

> Looking for the old Laravel Deployer? [Click here](https://github.com/lorisleiva/laravel-deployer/tree/old).

Laravel Deployer is no longer the package it used to be. Since that package was created, [Deployer](https://github.com/deployphp/deployer) has become better and better at integrating with Laravel to the point that I will now put my efforts into maintaining Deployer directly for Laravel users rather than mirroring its changes on a different repo after each release. You can [read more about this decision here](https://twitter.com/lorismatic/status/1376519608207867907?s=20).

I'm currently working on a series of tutorials to help Laravel users deploy their application using Deployer directly. In the meantime, here's a quick guide to get started with Deployer 7.

## Deploy your Laravel application using Deployer 7

- Add [Deployer](https://github.com/deployphp/deployer) to your dependencies.
  ```shell
  composer require deployer/deployer:^7.0
  ```
- Copy/paste the [`deploy.yaml`](./deploy.yaml) or [`deploy.php`](./deploy.php) file of this repository to the root of your project.
- Update the `deploy.yaml` or `deploy.php` with your own server details.
- Run `dep deploy` to deploy once.
- Run `dep ssh` to connect to your server via SSH.
- Run `cp .env.example && php artisan key:generate` to initialize your `.env` file.
- Run `vim .env` and update your production environment variables.
- Exit your server — `exit`.
- Run `dep deploy` once more now that your `.env` file is all set up.
