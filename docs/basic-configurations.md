# Basic configurations 

Following the instructions on the README page:

```bash
# install
composer require lorisleiva/laravel-deployer

# initialize configurations
php artisan deploy:init
```

You should end up with a `deploy.php` file at the root of your project.

**:fire: Pro tips:**
* Runing `php artisan deploy:init your.hostname.com -a`, will set you up with everything without asking you any questions.
* Using the `-f` option will optimize the configuration file for servers that are maintained by Laravel Forge.
* You can combine the two above.

Before starting you first deployment, you should go check your `deploy.php` file to make sure it suits your deployment flow.

## Check the options

```php
// The name of your application
set('application', 'Your Application Name');

// The repository of your application
set('repository', 'ssh://git@bitbucket.org/vendor/repository.git');

// Whether or not you allow Deployer to receive anonymous statistics from you.
set('allow_anonymous_stats', false);

// Allocate TTY for git clone command.
set('git_tty', true); 

// Default shared files are: `.env`. You can add more here.
add('shared_files', []);

// Default shared directories are: `storage`. You can add more here.
add('shared_dirs', []);

// Default writable directories are:
//     `bootstrap/cache`,
//     `storage`,
//     `storage/app`,
//     `storage/app/public`,
//     `storage/framework`,
//     `storage/framework/cache`,
//     `storage/framework/sessions`,
//     `storage/framework/views`,
//     `storage/logs`
// You can add more here.
add('writable_dirs', []);
```

* Run `php artisan deploy:configs` to see all options and their value.
* [Check out all available options](docs/all-options.md)


## Check the hosts

```php
// If you have more than one stage, you can define the default stage here.
set('default_stage', 'prod');

// Your hostname can be a domain or an IP address.
host('your.hostname.com')

    // (Optional) The stage of your host.
    // Can be useful to distinguish a production server and a staging server.
    ->stage('prod')
    
    // The deploy path. Where you code is.
    ->set('deploy_path', '/var/www/html/your.hostname.com')
    
    // (Optional) Who should Deployer connect as during deployment. 
    ->user('root');
```

* Deployer will access your server via SSH as the provided user.
* More authentication configurations can be found [here](host-configuration).


## Check the tasks and the hooks

Laravel Deployer already defines the main `deploy` task, therefore it is not visible in your `deployer.php` file.
If you just need to add more tasks before or after some other tasks, you can use hooks:

```php
before('existing:task', 'mycustom:task');
after('mycustom:task', 'myscustom:secondtask');
```

* [Check out how to create you custom tasks and use hooks](custom-tasks.md).
* [Check out all available tasks](all-tasks.md).

If some of the tasks used by default during deployment are unecessary for you or you need to redefine the deployment flow completely, you can override the `deploy` task:

```php
// This is the default definition of the `deploy` task.
// Copy/paste it in your `deploy.php` file if you want complete control over it.
desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'firstdeploy:shared',
    'deploy:vendors',
    'hook:build',
    'deploy:writable',
    'artisan:storage:link', // Not in Lumen applications
    'artisan:view:clear',   // Not in Lumen applications
    'artisan:cache:clear',
    'artisan:config:cache', // Not in Lumen applications
    'artisan:optimize',
    'hook:ready',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'hook:done',
    'success',
]);

after('deploy:failed', 'deploy:unlock');
```

**:fire: Pro tips:**
* Run `php artisan deploy:dump deploy` to display the tree of tasks that are executed during deployment including the ones added via hooks.
* Note that this command can do that for any given task `php artisan deploy:dump mycustom:task`.
