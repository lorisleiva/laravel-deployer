# How to deploy with Laravel Forge?

## Set up your deploy.php
Set up your host configurations by using the `forge` user and the `/home/forge/your.domain.com` deployment path. If your `~/.ssh/id_rsa` is set up and your `~/.ssh/id_rsa.pub` SSH key is already registered within forge, you're good to go. Otherwise you can [choose which SSH keys and configurations to use](overview-configure-hosts.md#authentication).

```php
// config/deploy.php

'hosts' => [
    'your.domain.com' => [
        'user' => 'forge',
        'deploy_path' => '/home/forge/your.domain.com',
    ]
],
```

**:fire: Pro tips:**
* When generating your `config/deploy.php` file, running: 
    
    ```bash
    php artisan deploy:init your.domain.com -f
    ```
    will use the `forge` user and provide better default values when asking you questions.
* Combine with the `-a` (`--all`) option to skip any console interaction whilst using a maximum amount of features — npm, migrations, horizon, etc.

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
Your deploy script in Forge is now unnecessary since your whole deployment logic is define by Laravel Deployer. If you still wish to deploy within Forge using Laravel Deployer — and potentially using the *Quick Deploy* feature — replace your script with the following:

```bash
cd /home/forge/your.domain.com/current
php artisan deploy
```

:warning: **For this script to work properly, you need to:**
1. Have `your.domain.com` registered in your server's trusted hosts.
      ```bash
        # Check if your.domain.com is trusted, otherwise add it.
        ssh-keygen -F your.domain.com || ssh-keyscan -H your.domain.com >> ~/.ssh/known_hosts
      ```

2. Register your server's ssh key on itself. This is important because when Forge's worker connects to your server, it runs `php artisan deploy` which itself starts a new ssh connection from the server to the server. Therefore the server must allow its own ssh key.
      ```bash
        # Copy your server's public RSA key.
        pbcopy < ~/.ssh/id_rsa.pub
      ```
      Paste it in the "SSH Keys" section of your server on Laravel Forge.

3. Finally, if you edit anything in your `config/deploy.php` file locally, your server's `current` directory won't be aware of those changes. Therefore a deployment within Laravel Forge will use the old deployment configurations.
    
    As a work around, whenever your deployment configurations change, you can either run `php artisan deploy` manually (not via Laravel Forge) or `git pull` directly in the `current` folder before deploying via Laravel Forge. Bear in mind that the latter doesn't offer zero-downtime deployment.

## Extra things to configure
* If you wish to reload php-fpm after each deployment like Forge does by default, you will need to use the `fpm:reload` task and set up your version of php-fpm within the `php_fpm_service` option. Read more [here](how-to-reload-fpm.md).
* If you have a Daemon running Horizon in Forge, [read this](how-to-horizon.md) to make sure it restarts after each deployment.
