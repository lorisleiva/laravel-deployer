# How to pass CLI variables to deployer?

Let's say you have a custom option called `my_deployer_variable` in your `deploy.php` file.

```php
// config/deploy.php

'options' => [
    'my_deployer_variable' => 'Default Value',
    // ...
],
```

And you'd like to define that option using a CLI environment variable called `MY_ENV_VARIABLE` whenever you deploy.

Then all you need to do is override that option when running the `php artisan deploy` command, like this:

```sh
php artisan deploy -o my_deployer_variable=$(echo MY_ENV_VARIABLE)
```

## Example 1: Custom deploy path

Let's say you have a CLI variable called `DEPLOY_PATH` in your CI/CD pipeline. Then you can override Deployer's deploy path like so:

```sh
php artisan deploy -o deploy_path=$(echo DEPLOY_PATH)
```

## Example 2: Deploy branches dynamically

You can also use this trick to override core options like `--branch` which defines which branch we should deploy.

For example, if you are using GitLab's pipeline, then the name of the current branch is made available using the following CLI variable: `$CI_COMMIT_REF_NAME`.

Therefore, you can tell Laravel Deployer to deploy the current pipeline branch by running the following command:

```sh
php artisan deploy --branch=$(echo $CI_COMMIT_REF_NAME)
```

You can even go one step further and provide a dynamic deploy path based on the branch name like so:

```php
// config/deploy.php

'example.com' => [
    'deploy_path' => '/var/www/feature/{{branch}}',
    // ...
]
```

If you'd like to know more about how to set up dynamic feature deployment using GitLab pipelines, you can read [this amazing answer](https://github.com/lorisleiva/laravel-deployer/issues/159#issuecomment-603734655) from [@pixelplant](https://github.com/pixelplant) detailing the process.
