# How to build your assets locally before deploying?

Building assets directly on your server can bring its performances down during deployments. If you'd rather build your application locally and then upload it to your hosts as the next release, you can use the local deployment strategy by running:

```bash
php artisan deploy:local
```

Since this deployment strategy generates two releases (one locally and one on the server), it tend to be a longuer deployment process.

## Task tree of deploy:local

```php
task('deploy:local', [
    'deploy:info',
    'local:build',          // Build your application locally
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'local:upload',         // Upload your locally-built application to your hosts
    'deploy:shared',
    'firstdeploy:shared',
    'deploy:writable',
    'artisan:storage:link', // Not in Lumen applications
    'artisan:view:clear',   // Not in Lumen applications
    'artisan:cache:clear',
    'artisan:config:cache', // Not in Lumen applications
    'artisan:optimize',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'local:cleanup',        // Remove locally-built application
    'hook:done',
    'success',
]);
```

## Local:build

The `local:build` task generates a release locally and uses the shallow hook `hook:build` to let you attach any necessary building tasks (like `npm:install` and `npm:production`).

```php
task('local:build', function() {
    set('deploy_path', get('local_deploy_path'));
    invoke('deploy:prepare');
    invoke('deploy:release');
    invoke('deploy:update_code');
    invoke('deploy:vendors');
    invoke('hook:build');       // Any tasks hooked to `hook:build` will be called locally
    invoke('deploy:symlink');
})->local();
```

By default the local release will be built in `vendor/lorisleiva/laravel-deployer/.build`. You can change this by changing the `local_deploy_path` option.

```php
set('local_deploy_path', 'my/custom/path');
```

## Local:upload

The `local:upload` task uses `rsync` to upload the local release to the server. By default, it excludes the `node_modules` folder since the assets should have been already compiled locally.

```php
set('local_upload_options', [
    'options' => [ '--exclude=node_modules' ],
]);
```

## Local:cleanup

The `local:cleanup` task will remove the entire folder used to build the release locally.