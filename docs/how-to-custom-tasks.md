# Custom tasks and hooks

## Hello world task

Tasks are very simple to define. You can leverage all available options like `{{release_path}}` to access the folder that is being built, or {{bin/php}} to ensure you are using the right PHP binary. You can also define and use your own options within your tasks to increase reusability. 

```php
set('my_custom_message', 'hello world!');

desc('My task');
task('my_task', function () {
    run("cd {{release_path}} && echo '{{my_custom_message}}'");
});
```

## Simple tasks

For tasks that simply need to execute some script on the server, you can define them in one line. By default all simple tasks cd to {{release_path}}, so you don't need to.

```php
task('my_task', "echo '{{my_custom_message}}'");
```

## Task grouping

You can define tasks as a chain of other tasks. For example, this is how the `deploy` task is constructed.

```php
task('deploy', [
    // Non-exhaustive list.
    'deploy:prepare',
    'deploy:update_code',
    'deploy:vendors',
    'deploy:symlink',
    'cleanup'
]);
```

## hooks

You can define tasks to be run before or after some tasks using hooks. This is especially useful to add some more tasks in your deployment flow in the right order.

```php
before('existing:task', 'mycustom:task');
after('mycustom:task', 'myscustom:secondtask');
```

Laravel Deployer provides shallow hooks to help you hook tasks into more strategic places of your deployment flow. [Read more](shallow-hooks.md).

## Filtering

You can define tasks to only run under a certain stage, role or hostname using the following methods.

```php
task('custom:task', function () { /* ... */ })
    ->onStage('prod')        // Filter by stage
    ->onRoles('app', 'db')   // Filter by roles
    ->onHosts('domain.com'); // Filter by hosts
```

Read more about tasks on the [official Deployer documentation](https://deployer.org/docs/tasks).