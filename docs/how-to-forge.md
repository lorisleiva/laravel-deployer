# How to deploy with Laravel Forge?

## Set up your deploy.php
[Enabling a localhost access](how-to-localhost.md) is particularly helpful when using [Laravel Forge](https://forge.laravel.com) to maintain your servers. Simply set up your configurations by using the `forge` user and the `/home/forge/your.domain.com` deployment path. If your `~/.ssh/id_rsa` SSH key is already registered within forge, you're good to go. Otherwise you can [configure which SSH key and configs to use](configure-hosts.md#authentication).

```php
set('default_stage', 'prod');

host('your.domain.com')
    ->stage('prod')
    ->set('deploy_path', '/home/forge/your.domain.com')
    ->user('forge');

localhost()
    ->stage('local')
    ->set('deploy_path', '/home/forge/your.domain.com')
    ->user('forge')
    ->set('git_tty', false);
```

**:fire: Pro tips:**
* When generating your `deploy.php` file, running: 
    ```bash
    php artisan deploy:init your.domain.com -f
    ```
  will generate an appropriate localhost configuration, use the `forge` user and provide better default values when asking you questions.
* Combine with the `-a` (`--all`) option to skip any console interaction whilst using a maximum of features — npm, migrations, horizon, etc.

## Edit your deploy script
Your deploy script in Forge is now unnecessary since your whole deployment logic is define by Laravel Deployer. Replace it with the following script to deploy using Laravel Deployer when you deloy in Forge.

```bash
cd /home/forge/your.domain.com/current
php artisan deploy local
```

Now if you have *Quick Deploy* turned on, every time you push to your repository, it will deploy using Laravel Deployer.

## Extra things to configure
* If you wish to reload php-fpm after each deployment like Forge does by default, you will need to use the `fpm:reload` task and set up your version of php-fpm within the `php_fpm_service` option. Read more [here](how-to-reload-fpm.md).
* If you have a Daemon running Horizon in Forge, [read this](how-to-horizon.md) to make sure it restarts after each deployment.