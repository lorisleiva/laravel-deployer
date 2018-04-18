# Create your own strategy

To create your own strategy, simply add an entry to the `strategies` option with a key and a list of tasks:

```php
// config/deploy.php

'strategies' => [
    'my_strategy' => [
        'deploy:prepare',
        'deploy:lock',
        'deploy:release',
        'deploy:update_code',
        'deploy:shared',
        'deploy:vendors',
        'hook:build',
        'deploy:writable',
        'hook:ready',
        'deploy:symlink',
        'deploy:unlock',
        'cleanup',
    ],
],
```

You can then use this key to choose a strategy:

```php
// config/deploy.php

'default' => 'my_strategy',
```

Don't forget to add hooks within your strategy if you have tasks defined in them.

```php
// config/deploy.php

'strategies' => [
    'my_strategy' => [
        'hook:start',           // Tasks hooked to `start` will be called here.
        'deploy:prepare',
        'deploy:lock',
        'deploy:release',
        'deploy:update_code',
        'deploy:shared',
        'deploy:vendors',
        'hook:build',           // Tasks hooked to `build` will be called here.
        'deploy:writable',
        'hook:ready',           // Tasks hooked to `ready` will be called here.
        'deploy:symlink',
        'deploy:unlock',
        'cleanup',
        'hook:done',            // Tasks hooked to `done` will be called here.
    ],
],
```


If you wish to add your own tasks within your custom strategies, you can [create and include your own recipes](how-to-custom-recipes.md). After that, your tasks will be available for you to use in your custom strategies.

```php
// config/deploy.php

'strategies' => [
    'my_strategy' => [
        'my_tasks:foobar',
        // ...
    ],
],

'include' => [
    'recipe/my_tasks.php',
],
```
