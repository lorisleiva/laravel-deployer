# What does a deploy do?

The following table describes the default deployment tasks in their execution order. This table does not include any hooks you might have in your `deploy.php` file, e.g. `artisan:migrate` or `npm:install`.

| task | description |
| - | - |
| `deploy:info` | Shows a little introduction message with the branch used to deploy and the current hostname. |
| `deploy:prepare` | Prepare the host to deploy, e.g. by creating the appropriate initial folder structure. |
| `deploy:lock` | Lock the host to ensure we won't try to redeploy before its done. |
| `deploy:release` | Clean up unfinished releases and prepare next release. |
| `deploy:update_code` | Update code by running `git clone` on the new release folder. |
| `firstdeploy:shared` | It will try to copy the shared files directly from the `{{deploy_path}}` if not available on the `{{release_path}}`. This ensures we do not lose shared files or directories that are git-ignored when deploying for the first time on a live server. [Read more here](first-deploy.md). |
| `deploy:shared` | Creating symlinks for shared files and directories. If shared files or directories are not present in the `shared` folder, it will try to resolve then from the `{{release_path}}`. |
| `deploy:vendors` | Installing vendors. Basically running `composer install`. |
| `deploy:writable` | Make directories in `{{writable_dirs}}` writable. |
| `artisan:storage:link` | Execute artisan `storage:link`. |
| `artisan:view:clear` | Execute artisan `view:clear`. |
| `artisan:cache:clear` | Execute artisan `cache:clear`. |
| `artisan:config:cache` | Execute artisan `config:cache`. |
| `artisan:optimize` | Execute artisan `optimize`. |
| `deploy:symlink` | Create symlink to release. Now that our release has been built, it will point the `current` directory to the `{{release_path}}` to make it live. |
| `deploy:unlock` | Release the deployment lock. |
| `cleanup` | Cleaning up old releases base on the `{{keep_releases}}` option. |
| `success` | 🍺 Display a success message. |

If anything goes wrong, the `deploy:failed` task will be executed to let us hook tasks on failure. By default Laravel Deployer ensures the lock is released when the deployment has failed using the following hook:

```php
after('deploy:failed', 'deploy:unlock');
```