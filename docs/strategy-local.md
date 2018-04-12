# Locally-built strategy

TODO

Building assets directly on your server can bring its performances down during deployments. If you'd rather build your application locally and then upload it to your hosts as the next release, you can use the *local deployment strategy* by running:

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
    'deploy:update_code',
    'local:upload',         // Synchronize your locally-built application with your host
    'deploy:shared',
    'firstdeploy:shared',
    'deploy:vendors',
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
    set('previous_release', get('local_cache_repository'));
    set('keep_releases', get('local_keep_releases'));

    invoke('deploy:prepare');
    invoke('deploy:release');
    invoke('deploy:update_code');
    invoke('deploy:vendors');
    invoke('hook:build');       // Any tasks hooked to `hook:build` will be called locally
    invoke('deploy:symlink');
    invoke('cleanup');
})->local();
```

* By default the local release will be built in `vendor/lorisleiva/laravel-deployer/.build`. You can change this by overriding the `local_deploy_path` option.

  ```php
  set('local_deploy_path', 'my/custom/path');
  ```

* By default the root of your application will be used as the local `previous_release`. This enables better performances when locally cloning your git repository or installing node_modules. You can change this by overriding the `local_cache_repository` option.

* By default the local build of your application will only keep one release since the entire `local_deploy_path` will be deleted after deployment. You can change this by overriding the `local_keep_releases` option.

## Local:upload

The `local:upload` task uses `rsync -azP --delete` to synchronize the local release with the server's release. By default, it excludes the following folders:
* The `.git` folder since its irrelevant to the server's release.
* The `vendor` folder since its more efficient to do a `deploy:vendors` directly on the server.
* The `node_modules` folder since the assets have been already compiled locally.

```php
set('local_upload_options', [
    'options' => [ 
        '--exclude=.git',
        '--exclude=vendor',
        '--exclude=node_modules',
    ],
]);
```

## Local:cleanup

The `local:cleanup` task will remove the entire folder used to build the release locally, i.e. the `local_deploy_path` folder.