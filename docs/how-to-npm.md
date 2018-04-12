# How to compile assets on deploy using npm?

The recipe `recipe/npm.php` is already required within Laravel Deployer's recipe. Therefore, all you need to do is hook the `npm` tasks in you deployment flow.

```php
// config/deploy.php
    
'hooks' => [
    'build' => [
        'npm:install',
        'npm:production',
    ],
],
```

# Available tasks

| task  | description |
| - | - |
| `npm:install` | Copy the `node_modules` folder from the previous release if it exists, and run `npm install`. |
| `npm:development` | Compile your assets using `npm run develoment`. |
| `npm:production` | Compile your assets using `npm run production`. |