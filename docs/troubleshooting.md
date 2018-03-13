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

## My changes don't show up after a deployment.

**Problem:**

You push some new commits and run `php artisan deploy` successfully but the changes don't seem to appear on your application.

**Solution:**

Since we are using symlinks to link the `current` folder to the current release of your application, it is likely that OPcache does not properly detect changes to your PHP files. To fix this, you should pass the real application path instead of the path to the symlink to PHP FPM. Add the following lines after the rest of your `fastcgi` configuration in the `location` block of your nginx configurations.

```nginx
fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
fastcgi_param DOCUMENT_ROOT $realpath_root;
```

If this doesn't fix the problem, make sure that your nginx configurations allow symbolic links by adding `disable_symlinks off;` to your server block.

```nginx
server {
    # ...
    disable_symlinks off;
}
```