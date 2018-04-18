# Configure your hosts

## Basic configurations

When defining a host, you need to provide it with a hostname. It can be the domain of your server or its IP address. You also need to provide a `deploy_path` to your host which tells Laravel Deployer where your application should be deployed within your host.

```php
// config/deploy.php

'hosts' => [
    'domain.com' => [
        'deploy_path' => '/var/www/acme',
    ],
],
```

You can also give your hosts a stage and/or several roles.

```php
// config/deploy.php

'hosts' => [
    'domain.com' => [
        // ...
        'stage' => 'production',
        'roles' => 'app',
    ],
],
```

You can use those later on to filter hosts when executing tasks.

```bash
php artisan deploy production
php artisan deploy --roles=app
php artisan deploy --hosts='domain.com'
```

Finally, you can set up and override any options locally within a host. In the following example, `key` equals 'global' for every hosts except for the 'domain.com' host where `key` equals 'local'.

```php
// config/deploy.php

'options' => [
    'key' => 'global',
],
'hosts' => [
    'domain.com' => [
        // ...
        'key' => 'local',
    ],
],
```

## Authentication

By default, deployer will use your `~/.ssh/id_rsa` key to connect to your host. If you want a custom SSH set up, use the following set of options.

```php
// config/deploy.php

'hosts' => [
    'domain.com' => [
        // ...
        'user'         => 'root',
        'configFile'   => '~/.ssh/config',
        'identityFile' => '~/.ssh/id_rsa',
        'forwardAgent' => true,
        'multiplexing' => true,
        'sshOptions'   => [ 
            'UserKnownHostsFile' => '/dev/null',
            'StrictHostKeyChecking' => 'no',
            // ...
        ],
    ],
],
```

If you need more control over the configuration of your hosts, you first need to [create and include your own recipe](how-to-custom-recipes.md) and then read the official [Deployer documentation on hosts](https://deployer.org/docs/hosts).