# How to terminate horizon?

If you have a Daemon running `php artisan horizon`, you might want to terminate horizon everytime you deploy to restart it. You can use the `artisan:horizon:terminate` for that purpose.

```php
// config/deploy.php
    
'hooks' => [
    'ready' => [
        // ...
        'artisan:horizon:terminate',
    ],
],
```