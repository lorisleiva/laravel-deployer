# How to use my own deployer file?

By default, Laravel Deployer uses your `config/deploy.php` file to generate a new Deployer file every time one of its commands. If you feel comfortable enough to manipulate the deployer file on your own — and still want to use Laravel Deployer's commands — you can provide its path on the configuration file:

```php
// config/deploy.php

return [
    'custom_deployer_file' => 'path/to/my/deploy.php',
];
```

Alternatively, if your Deployer file is at the root of your project, you can delete the `config/deploy.php` file altogether and Laravel Deployer will use it for its commands.