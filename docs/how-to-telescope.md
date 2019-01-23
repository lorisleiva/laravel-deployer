# How to clear Telescope entries?

If you are using Laravel's Telescope, you might want to clean up Telescope's entry table as data can get accumulated quickly.

Telescope includes two console commands to clean up the entries table: 
- `telescope:clean`: this will delete all entries
- `telescope:prune`: this will clear all entries up until a given date/time. By default Telescope will retain entries made in the last 48 hours.

When deploying to a Production environment, you typically don't need to enable Telescope. However, if you use a testing or staging environment for example, you may want to clean the Telescope data with a deployment.

You can use the `artisan:telescope:prune` to prune Telescope data with the following configuration:

```php
// config/deploy.php
    
'hooks' => [
    'ready' => [
        // ...
        'artisan:telescope:prune',
    ],
],
```

If you don't need to keep the entries of the last 48 hours, you can use the `artisan:telescope:clear` to entirely clear Telescope's entry table like this:

```php
// config/deploy.php
    
'hooks' => [
    'ready' => [
        // ...
        'artisan:telescope:clear',
    ],
],
```

