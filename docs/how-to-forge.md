# How to set up Laravel Forge?

TODO: Update. Again, localhost really necessary?

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
    ->user('forge');
```

**:fire: Pro tips:**
* When generating your `config/deploy.php` file, running: 
    ```bash
    php artisan deploy:init your.domain.com -f
    ```
  will use the `forge` user and provide better default values when asking you questions.
* Combine with the `-a` (`--all`) option to skip any console interaction whilst using a maximum of features — npm, migrations, horizon, etc.

## Update web directory
Go to the *Meta* tab of your application page on Forge and change it to the `/current/public` directory.

![Enter `/current/public` on Meta > Update Web Directory](https://user-images.githubusercontent.com/3642397/37337948-320f3ea0-26b6-11e8-902f-f4b185c609c7.png)

## Make OPcache work with symlinks
Next, you need to pass the real application path instead of the symlink path to PHP FPM. Otherwise, PHP's OPcache may not properly detect changes to your PHP files. Add the following lines after the rest of your `fastcgi` configurations in the `location` block of your nginx configurations.

```nginx
fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
fastcgi_param DOCUMENT_ROOT $realpath_root;
```

![Edit your nginx configuration file](https://user-images.githubusercontent.com/3642397/37338252-30323c08-26b7-11e8-85d5-db49d5c4abbe.png)

![Add more fastcgi configs to your location block](https://user-images.githubusercontent.com/3642397/37346220-11785cb2-26cf-11e8-906e-30da2bfbe847.png)

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