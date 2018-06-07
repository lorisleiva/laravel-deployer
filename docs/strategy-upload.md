# Upload strategy

It might be the case that your local folder is already production-ready and simply needs to be uploaded to your hosts as a new release. For example, if you deploy directly via GitLab's pipeline and you've already built all of your assets. This strategy takes care of that for you.

![Upload strategy diagram](https://user-images.githubusercontent.com/3642397/41128058-5222389c-6aad-11e8-89f5-f054541d69f5.png)

:warning: Note that there is no `hook:build` because this strategy assumes the uploaded folder has already been built.

More information about available tasks can be found [here](all-tasks.md).

## Tasks specific to the local strategy

### Upload

The `upload` task uses `rsync -azP --delete` to synchronize a given local folder with the new server's release.

The default path being uploaded to your server is the root of your application. You can configure this using the `upload_path` option.

```php
// config/deploy.php

'options' => [
    'upload_path' => __DIR__ . '/..',
],
```

By default, the upload excludes the following folders:
* The `.git` folder since its irrelevant to the server's release.
* The `vendor` folder since its more efficient to do a `deploy:vendors` directly on the server.
* The `node_modules` folder since the assets have been already compiled locally.

```php
// config/deploy.php

'options' => [
    'upload_options' => [
        'options' => [ 
            '--exclude=.git',
            '--exclude=vendor',
            '--exclude=node_modules',
        ],
    ],
],
```
