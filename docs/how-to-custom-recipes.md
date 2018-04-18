# How to create your own tasks and recipes?

## 1. Create and include your recipe

1. Create a `recipe` folder at the root of your project (or anywhere you'd like as long as it does not conflict with namespaces).
    
2. Create a `my_recipe.php` file inside that folder, starting with the following code:
    
    ```php
    <?php

    namespace Deployer;

    // Recipe content here...
    ```
        
3. Add the recipe to your config file:
    
    ```php
    // config/deploy.php

    'include' => [
        'recipe/my_recipe.php',
    ],
    ```

## 2. Configure your own options

### Setting options
Options are just another key/value storage used to configure tasks. You can set up new one (e.g. to use in your own tasks) or override some existing ones to edit the behavior of an existing task.

```php
// Create your own options.
set('key', 'value');

// Override existing ones.
set('deploy_path', '/custom/deploy/path');

// You can set and append to arrays.
set('shared_files', [ '.env' ]);
add('shared_files', [ 'foobar.md' ]);

// Values can also be callbacks.
set('lazy_loaded_value', function () {
    return 42;
});
```

As you can see, defining options within recipes gives you more flexibility. However, if all you need if a simple `set('key', 'value');`, you can equivalently add it to your `config/deploy.php` file instead:

```php
// config/deploy.php

'options' => [
    'key' => 'value',
],
```

### Getting options
Options can be retrieve in tasks using the `get()` method, or using mustache notation within the `run` method.

```php
task('custom:task', function () {
    $param = get('param');
    run("cd {{release_path}} && command $param");
});
```

## 3. Add tasks to your recipe

### Hello world task

Tasks are very simple to define. You can leverage all available options like `{{release_path}}` to access the folder that is being built, or {{bin/php}} to ensure you are using the right PHP binary. You can also define and use your own options within your tasks to increase reusability. 

```php
set('my_custom_message', 'hello world!');

desc('My task');
task('my_task', function () {
    run("cd {{release_path}} && echo '{{my_custom_message}}'");
});
```

You can also invoke other tasks within your task via the `invoke` function.

```php
task('my_task', function () {
    invoke('other:task');
});
```

### Simple tasks

For tasks that simply need to execute some script on the server, you can define them in one line. By default all simple tasks cd to {{release_path}}, so you don't need to.

```php
task('my_task', "echo '{{my_custom_message}}'");
```

### Task grouping

You can define tasks as a chain of other tasks. This is how most deployment strategies are constructed.

```php
task('strategy:basic', [
    // Non-exhaustive list.
    'deploy:prepare',
    'deploy:update_code',
    'deploy:vendors',
    'deploy:symlink',
    'cleanup'
]);
```

### Filtering

You can define tasks to only run under a certain stage, role or hostname using the following methods.

```php
task('custom:task', function () { /* ... */ })
    ->onStage('prod')        // Filter by stage
    ->onRoles('app', 'db')   // Filter by roles
    ->onHosts('domain.com'); // Filter by hosts
```

Read more about tasks on the [official Deployer documentation](https://deployer.org/docs/tasks).

## 4. Use custom hooks

You can define tasks to be run before or after some tasks using hooks. This is especially useful to add some more tasks in your deployment flow in the right order.

```php
before('existing:task', 'mycustom:task');
after('mycustom:task', 'myscustom:secondtask');
```

Laravel Deployer provides shallow hooks to help you hook tasks into more strategic places of your deployment flow. For example, the following code:

```php
after('hook:build', 'mycustom:task');
```

is equivalent to:

```php
// config/deploy.php

'hooks' => [
    'build' => ['mycustom:task'],
],
```
