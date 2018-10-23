# How to compile assets on deploy using npm (or yarn)?

The recipes `recipe/npm.php` and `recipe/yarn.php` are already required within Laravel Deployer's recipe. Therefore, all you need to do is hook the relevant tasks in you deployment flow.

```php
// config/deploy.php
    
'hooks' => [
    'build' => [
        'npm:install',    // or yarn:install
        'npm:production', // or yarn:production
    ],
],
```

# Available tasks

| task  | description |
| - | - |
| `npm:install` | Copy the `node_modules` folder from the previous release if it exists, and run `npm install`. |
| `npm:development` | Compile your assets using `npm run develoment`. |
| `npm:production` | Compile your assets using `npm run production`. |
| `yarn:install` | Copy the `node_modules` folder from the previous release if it exists, and run `yarn install`. |
| `yarn:development` | Compile your assets using `yarn develoment`. |
| `yarn:production` | Compile your assets using `yarn production`. |