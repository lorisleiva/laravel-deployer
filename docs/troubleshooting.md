# 🚨 Troubleshooting

## Unable to setup correct permissions for writable dirs.

**Problem:**

```bash
[RuntimeException]
Cant't set writable dirs with ACL.
```

**Solution:**

Since Deployer uses `acl` by default to set up permissions for writable directories, this is likely that your server does not have it installed yet. Running the following command (on linux) and restarting deployment should fix that for you.

```bash
sudo apt-get install acl
```

## My changes don't show after a deployment.

**Problem:**

You push some new commits and run `php artisan deploy` successfully but the changes don't seem to appear on your application.

**Solution:**

It is likely that your server configurations do not allow symlinks. Since the `current` folder is a symlink, it will not see it as linked to the newest release. To allow symlinks in your nginx configuration, add `disable_symlinks off;` to your server block.


```
server {
    # ...
    disable_symlinks off;
}
```