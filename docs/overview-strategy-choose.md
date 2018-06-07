# Choose the right strategy

Strategies are collections of tasks that defines a certain way to deploy your application. This page gives you an overview of all available strategies after explaining how to choose a default strategy and how to give a particular host its own strategy.

## Set up a default strategy

Simply set up your `default` configuration to the strategy of your choice. See the overview below to help you choose the right strategy.

```php
// config/deploy.php

'default' => 'basic',
```

## Give a particular host its own strategy

In the example below, `dev.domain.com` and `staging.domain.com` will be using the `basic` strategy whereas the production host `domain.com` will be using the `local` strategy.

```php
// config/deploy.php

'default' => 'basic',

'hosts' => [
    'dev.domain.com' => [],
    'staging.domain.com' => [],  
    'domain.com' => [
        'strategy' => 'local',
    ],  
]
```

## Choose your strategy when running the command

You can override your default strategy when running `php artisan deploy` by providing the `--strategy` or `-s` option.

```bash
# This enables you to run a quick git pull update without editing your configurations.
php artisan deploy -s pull
```

## Overview of available strategies

| Strategy | Description | Quick schema |
| - | - | - |
| **basic** ([doc](strategy-basic.md)) | Simple deployment process that takes place inside the host intself | ![Overview basic schema](https://user-images.githubusercontent.com/3642397/38679147-4369458c-3e63-11e8-8888-e062dcbbff09.png) |
| **firstdeploy** ([doc](strategy-first-deploy.md)) | Simple deployment process optimised for first deployments on already live hosts. | ![Overview firstdeploy schema](https://user-images.githubusercontent.com/3642397/38944793-069f46f2-4335-11e8-9b89-4c9e11e885a4.png) |
| **local** ([doc](strategy-local.md)) | Builds your release locally and upload it to your server when it's ready. | ![Overview local schema](https://user-images.githubusercontent.com/3642397/38679148-43898e82-3e63-11e8-9810-3d5d81116a2a.png) |
| **pull** ([doc](strategy-pull.md)) | Simply runs `git pull` in your current folder, therefore does not provide zero-downtime. | ![Overview pull schema](https://user-images.githubusercontent.com/3642397/39048055-7d20fe0c-449c-11e8-87cc-e5d9a9f09066.png) |
| **upload** ([doc](strategy-upload.md)) | Uploads a production-ready folder directly within a new release. | ![Overview upload schema](https://user-images.githubusercontent.com/3642397/41128457-7f1d3026-6aae-11e8-93e2-98a23973df5d.png) |


Learn how to create your own strategy [here](overview-strategy-create.md).

**:fire: Pro tips:**
* Run `php artisan deploy:dump strategy:basic` (replace `basic` with the strategy you use) to display the tree of tasks that are executed during deployment including the ones added via hooks.
* Note that this command can do that for any given task `php artisan deploy:dump mycustom:task`.
