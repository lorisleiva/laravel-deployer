# Shallow hooks

Laravel Deployer provides 3 shallow tasks that don't execute any code at all but help you hook tasks in more strategic places of your deployment flow.

```bash
hook:build  # Code and composer vendors are ready but nothing is built
hook:ready  # Deployment is done but not live yet (before symlink)
hook:done   # Deployment is done and live
```

For example, let's say you want to migrate and terminate horizon in your deployment flow. Without using shallow hooks you would write:

```php
before('deploy:symlink', 'artisan:migrate');
after('artisan:migrate', 'artisan:horizon:terminate');
```

With shallow hooks you can write:

```php
after('hook:ready', 'artisan:migrate');
after('hook:ready', 'artisan:horizon:terminate');
```