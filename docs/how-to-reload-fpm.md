# How to reload php-fpm?

In order to reload php-fpm after each deployment, you will need to add the `fpm:reload` task at the end of your deployment flow (including, if necessary, rolling back to a previous version). You also have to set up which version of php-fpm you are using.

```php
// config/deploy.php

'options' => [
    'php_fpm_service' => 'php7.1-fpm',
],
'hooks' => [
    'done' => ['fpm:reload'],
    'rollback' => ['fpm:reload'],
],
```

If you wish to customize the command executed by `fpm:reload`, you can override the `php_fpm_command` option.

```php
// config/deploy.php

'options' => [
    'php_fpm_command' => 'echo "" | sudo -S /usr/sbin/service {{php_fpm_service}} reload',
],
```
