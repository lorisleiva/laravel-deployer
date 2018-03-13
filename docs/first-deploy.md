# First deploy on a live project

Deploying for the first time will create a new folder structure within your deployment path to include:
* `.dep/` some Deployer configurations.
* `current/` (symlink) the current live release.
* `releases/` the latest releases deployed.
* `shared/` the folders and files shared between releases.

When deploying on a server that already contains live code, you have some extra steps to follow.

## 1️⃣ Deploy normally
Start by deploying using `php artisan deploy`. Unless you have some folders called `current`, `releases` or `shared` in your project, it should not be a problem. The first deployment will also take care of copying shared files and directories from the deployment path into the shared path.

> **What do you mean?**
> 
> If some of your shared files or directories are included in your `.gitignore`, the native `deploy:shared` task will not be able to retrieve them from your repository. This is typically the case for our `.env` file and the `storage` folder. If you already have a `.env` file and a `storage` folder in your live deployment path, you might want to use them in your shared folder.
> 
> By default Laravel Deployer provides a `firstdeploy:shared` task (executed right before the `deploy:shared` task) that will copy any shared files and folders present in the `deploy_path` (but absent in the `release_path`) into the shared folder.

## 2️⃣ Point root path to current directory
Make sure your server's root path points to the `current` symlink. For example if your `deploy_path` is `var/www/domain.com`, your server configurations should point to `var/www/domain.com/current`.

If you're using OPcache, you need to pass the realpath of the application instead of its symbolic link. Otherwise, PHP's OPcache may not properly detect changes to your PHP files. Add the following lines after the rest of your `fastcgi` configurations in the `location` block of your nginx configurations.

```nginx
fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
fastcgi_param DOCUMENT_ROOT $realpath_root;
```

Are you using Laravel Forge? [You can do this directly from the web interface](how-to-forge.md#update-web-directory).

## 3️⃣ Cleanup old code.
Now that your application is pointing to the `current` symlink, you do not need any of the files and folders at the root of your deployment path that aren't part of the Deployer folders, i.e. `.dep`, `current`, `releases`, `shared`.

You can either remove them manually or run the `firstdeploy:cleanup` task provided by Laravel Deployer. The task will show you the files and folders that will be deleted and ask you for confirmation before deleting anything.

```bash
php artisan deploy:run firstdeploy:cleanup
```