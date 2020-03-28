# All tasks

The following table lists all tasks available by default. If you add your own, or pull in other recipes, you can list them using:

```bash
php artisan deploy:list            # List all tasks
php artisan deploy:list namespace  # List all tasks starting with `namespace:`
```

| task | description |
| - | - |
| `autocomplete`              | Install command line autocompletion capabilities |
| `cleanup`                   | Cleaning up old releases |
| `deploy`                    | Deploy your project |
| `help`                      | Displays help for a command |
| `init`                      | Initialize deployer in your project |
| `list`                      |  Lists commands |
| `rollback`                  | Rollback to previous release |
| `run`                       | Run any arbitrary command on hosts |
| `ssh`                       | Connect to host through ssh |
| `artisan:cache:clear`       | Execute artisan cache:clear |
| `artisan:config:cache`      | Execute artisan config:cache |
| `artisan:config:clear`      | Execute artisan config:clear |
| `artisan:db:seed`           | Execute artisan db:seed |
| `artisan:down`              | Enable maintenance mode |
| `artisan:event:cache`       | Execure artisan event:cache |
| `artisan:event:clear`       | Execure artisan event:clear |
| `artisan:horizon:assets`    | Execute artisan horizon:assets |
| `artisan:horizon:publish`   | Execute artisan horizon:publish |
| `artisan:horizon:terminate` | Execute artisan horizon:terminate |
| `artisan:migrate`           | Execute artisan migrate |
| `artisan:migrate:fresh`     | Execute artisan migrate:fresh |
| `artisan:migrate:rollback`  | Execute artisan migrate:rollback |
| `artisan:migrate:status`    | Execute artisan migrate:status |
| `artisan:optimize`          | Execute artisan optimize |
| `artisan:optimize:clear`    | Execute artisan optimize:clear |
| `artisan:queue:restart`     | Execute artisan queue:restart |
| `artisan:route:cache`       | Execute artisan route:cache |
| `artisan:storage:link`      | Execute artisan storage:link |
| `artisan:up`                | Disable maintenance mode |
| `artisan:view:cache`        | Execute artisan view:cache |
| `artisan:view:clear`        | Execute artisan view:clear |
| `artisan:telescope:clear`   | Execute artisan telescope:clear |
| `artisan:telescope:prune`   | Execute artisan telescope:prune |
| `artisan:telescope:publish` | Execute artisan telescope:publish |
| `artisan:nova:publish`      | Execute artisan nova:publish |
| `config:current`            | Show current paths |
| `config:dump`               | Print host configuration |
| `config:hosts`              | Print all hosts |
| `debug:task`                | Display the task-tree for a given task |
| `deploy:clear_paths`        | Cleaning up files and/or directories |
| `deploy:copy_dirs`          | Copy directories |
| `deploy:lock`               | Lock deploy |
| `deploy:prepare`            | Preparing host for deploy |
| `deploy:public_disk`        | Make symlink for public disk |
| `deploy:release`            | Prepare release. Clean up unfinished releases and prepare next release |
| `deploy:shared`             | Creating symlinks for shared files and dirs |
| `deploy:symlink`            | Creating symlink to release |
| `deploy:unlock`             | Unlock deploy |
| `deploy:update_code`        | Update code |
| `deploy:vendors`            | Installing vendors |
| `deploy:writable`           | Make writable dirs |
| `firstdeploy:cleanup`       | Deletes everything from deploy path that isn't from deployer |
| `firstdeploy:shared`        | Copying the shared folder from the deploy path if possible |
| `fpm:reload`                | Reload the php-fpm service |
| `local:build`               | Build your application locally |
| `local:cleanup`             | Remove locally-built application |
| `local:upload`              | Upload your locally-built application to your hosts |
| `npm:development`           | Execute npm run development |
| `npm:install`               | Install npm packages |
| `npm:production`            | Execute npm run production |
| `yarn:development`           | Execute yarn development |
| `yarn:install`               | Install yarn packages |
| `yarn:production`            | Execute yarn production |
