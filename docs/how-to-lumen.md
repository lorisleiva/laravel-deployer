# How to deploy a Lumen application?

Some default artisan commands when deploying a Laravel application are not relevant to a Lumen application. Therefore, another recipe is available when deploying the latter.

In your `deploy.php` file, replace the default `require 'recipe/laravel-deployer.php';` with:

```php
require 'recipe/lumen-deployer.php';
```

:fire: **Note that** when running `php artisan deploy:init`, the command will automatically detect if your application is a Lumen application and require the appropriate recipe.