# Locally-built strategy

Building assets directly on your server can bring its performances down during deployments. If you'd rather build your application locally and then upload it to your hosts as the next release, you can use the `local` strategy.

![Local strategy diagram](https://user-images.githubusercontent.com/3642397/38677820-a775c720-3e5f-11e8-8d75-f0f3db60b246.png)

Note that this deployment strategy generates two releases (one locally and one on the server). Thus, it tend to be a longuer deployment process.

More information about available tasks can be found [here](all-tasks.md).

## Tasks specific to the local strategy

### Local:build

The `local:build` task generates a release locally and uses the shallow hook `hook:build` to let you attach any necessary building tasks (like `npm:install` and `npm:production`).

```php
task('local:build', function() {
    set('deploy_path', get('local_deploy_path'));
    set('previous_release', get('local_cache_repository'));
    set('keep_releases', get('local_keep_releases'));

    invoke('deploy:prepare');
    invoke('deploy:release');
    invoke('deploy:update_code');
    copyShared('{{previous_release}}', '{{release_path}}');
    invoke('deploy:shared');
    invoke('deploy:vendors');
    invoke('hook:build');       // Any tasks hooked to `build` will be called locally
    invoke('deploy:symlink');
    invoke('cleanup');
})->local();
```

* By default the local release will be built in `vendor/lorisleiva/laravel-deployer/.build`. You can change this by overriding the `local_deploy_path` option.

  ```php
  // config/deploy.php
  
  'options' => [
      'local_deploy_path' => 'my/custom/path',
  ],
  ```

* By default the root of your application will be used as the local `previous_release`. This enables better performances when locally cloning your git repository or installing node_modules. You can change this by overriding the `local_cache_repository` option.

* By default the local build of your application will only keep one release since the entire `local_deploy_path` will be deleted after deployment. You can change this by overriding the `local_keep_releases` option.

* Between the `deploy:update_code` and the `deploy:shared` tasks, we try to populate the shared folder with the `local_cache_repository`'s content. This is particularly helpful for shared files and folders that are gitignored, e.g. `.env` and `storage/`.

### Local:upload

The `local:upload` task uses `rsync -azP --delete` to synchronize the local release with the server's release. By default, it excludes the following folders:
* The `.git` folder since its irrelevant to the server's release.
* The `vendor` folder since its more efficient to do a `deploy:vendors` directly on the server.
* The `node_modules` folder since the assets have been already compiled locally.

```php
// config/deploy.php

'options' => [
    'local_upload_options' => [
        'options' => [ 
            '--exclude=.git',
            '--exclude=vendor',
            '--exclude=node_modules',
        ],
    ],
],
```

### Local:cleanup

The `local:cleanup` task will remove the entire folder used to build the release locally, i.e. the `local_deploy_path` folder.
