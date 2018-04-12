# How to deploy a Lumen application?

Some default artisan commands when deploying a Laravel application are not relevant to a Lumen application. Therefore, when deploying a Lumen application, you need to remove unsupported artisan commands from the `ready` hook.

In your `config/deploy.php` file, use the following tasks in your `ready` hook:

```php
// config/deploy.php

'hooks' => [
    'ready' => [
        // 'artisan:storage:link',  <-- Not supported by Lumen
        // 'artisan:view:clear',    <-- Not supported by Lumen
        'artisan:cache:clear',
        // 'artisan:config:cache',  <-- Not supported by Lumen
        'artisan:optimize',
    ],
],
```

:fire: **Note that** when running `php artisan deploy:init`, the command will automatically detect if your application is a Lumen application and use the appropriate artisan commands.