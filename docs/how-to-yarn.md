# How to compile assets on deploy using yarn?

The recipe `recipe/yarn.php` is already required within Laravel Deployer's recipe. Therefore, all you need to do is hook the `yarn` tasks in you deployment flow.

```php
// config/deploy.php
    
'hooks' => [
    'build' => [
        'yarn:install',
        'yarn:production',
    ],
],
```

# Available tasks

| task  | description |
| - | - |
| `yarn:install` | Copy the `node_modules` folder from the previous release if it exists, and run `yarn install`. |
| `yarn:development` | Compile your assets using `yarn develoment`. |
| `yarn:production` | Compile your assets using `yarn production`. |
