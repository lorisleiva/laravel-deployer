# Available options and defaults

## Setting options
Options are just another key/value storage used to configure tasks without having to rewrite them. You can set up new one (to use in your own tasks for example) or override some existing ones to edit the behavior of an existing task.

```php
// Create your own options.
set('key', 'value');

// Override existing ones.
set('deploy_path', '/custom/deploy/path');

// You can also set and append to arrays.
set('shared_files', [ '.env' ]);
add('shared_files', [ 'foobar.md' ]);
```

## Getting options
Options can be retrieve in tasks using the `get()` method, or using mustache notation within the `run` method.

```php
task('custom:task', function () {
    $param = get('param');
    run("cd {{release_path}} && command $param");
});
```

## Existing options
| key | default | description |
| - | - | - |
| `deploy_path` | | Where to deploy application on remote host. You should define this variable for all of your hosts. |
| `hostname` | | Current hostname. Automatically set by host function. |
| `user` | Current git username | Current user name. |
| `release_path` | | Full path to the current release directory. Current dir path in non-deploy contexts. Use it as working path for your build |
| `previous_release` | | Points to previous release if it exists. Otherwise variable doesn't exists. |
| `ssh_multiplexing` | `true` | Use ssh multiplexing to speedup the native ssh client. |
| `stage` | | Current stage. |
| `default_stage` | | If the hosts declaration has stages, this option allows you to select the default stage to deploy to. |
| `keep_releases` | 5 | Number of releases to keep. -1 for unlimited releases. |
| `repository` | | Git repository of the application. |
| `git_tty` | `false` | Allocate TTY for git clone command. This allow you to enter a passphrase for keys or add host to known_hosts. |
| `git_recursive` | `true` | Set the --recursive flag for git clone. Setting this to false will prevent submodules from being cloned as well. |
| `branch` | master | Branch to deploy. |
| `shared_dirs` | `[ "storage" ]` | List of shared directories. |
| `shared_files` |  `[ ".env" ]` | List of shared files. |
| `copy_dirs` | `[]` | List of files to copy between release. |
| `writable_dirs` | `[ "bootstrap/cache", "storage", "storage/app", "storage/framework", "storage/logs", ... ]` | List of directories which must be writable for web server. |
| `writable_mode` | `acl` | Writable mode. Choice of `acl`, `chmod`, `chown`, `chgrp`. |
| `writable_use_sudo` | `false` | Whether to use sudo with writable command. |
| `writable_chmod_mode` | `0755` | Mode for setting writable_mode in chmod. |
| `writable_chmod_recursive` | `true` | Whether to set chmod on dirs recursively or not. |                
| `http_user` | `false` | User the web server runs as. If this parameter is not configured, deployer try to detect it from the process list. |
| `clear_paths` | [] | List of paths which need to be deleted in release after updating code. |
| `clear_use_sudo` | `false` | Use or not sudo with clear_paths. |
| `cleanup_use_sudo` | `false` | Whether to use sudo with cleanup task. |
| `use_relative_symlink` | | Whether to use relative symlinks. By default deployer will detect if the system supports relative symlinks and use them. |
| `use_atomic_symlink` | | Whether to use atomic symlinks. By default deployer will detect if system supports atomic symlinks and use them. |
| `composer_action` | `install` | Composer action. |
| `composer_options` | `{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader` | Options for Composer including the action. This is what is used by the `deploy:vendors` task. |
| `env` | `[]` | Array of environment variables. |
| `releases_list` | `[...]` | List the names of the existings releases. |
| `release_name` | | Function that returns the name of the next release. Override to provide your own release_name logic (e.g YmdHis format). |
| `current_path` | | Path of the currently active release. |
| `bin/git` | | Git binary.
| `bin/php` | | PHP binary.
| `bin/composer` | | Composer binary.
| `bin/symlink` | | Symlink binary.
| `bin/npm` | | Npm binary.
| `laravel_version` | | Version of Laravel used.
| `php_fpm_service` | | Name of the php-fpm service. |
| `php_fpm_command` | `echo "" \| sudo -S /usr/sbin/service {{php_fpm_service}} reload` | Command used to reload the php-fpm service. |
| `application` | | Application name. |
| `allow_anonymous_stats` | | Whether or not you allow Deployer to receive anonymous statistics from you. |