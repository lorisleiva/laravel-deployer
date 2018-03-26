# How to deploy directly from server?

When deploying directly from a host, it will still try to connect to itself via SSH which can lead to some extra unecessary burden. To make it easier for ourselves, we can define a `localhost` that will be used when the host matches the current server. You can set up the `localhost` just like any other hosts.

```php
localhost()
    ->stage('local')
    ->set('deploy_path', '/var/www/your.domain.com')
    ->user('root');
```

When running `php artisan deploy local` from your server it will use your localhost configurations and will not try to SSH connect to itself. If you'd rather not use any stage, you can also run `php artisan deploy localhost`.

However, running `php artisan deploy` on your local machine will now also try to deploy on your computer. To prevent this, you can give your host a dedicated stage and make it your default deployment stage.

```php
set('default_stage', 'prod');

host('your.domain.com')
    // ...
    ->stage('prod');

localhost()
    // ...
    ->stage('local')
```

With this configuration, running:
* `php artisan deploy` will only deploy on the server and
* `php artisan deploy local` will only deploy locally.