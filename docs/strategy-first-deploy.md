# First deploy strategy


Deploying for the first time will create a new folder structure within your deployment path to include:
* `.dep/` some Deployer configurations.
* `current/` (symlink) the current live release.
* `releases/` the latest releases deployed.
* `shared/` the folders and files shared between releases.

This strategy is very similar to the basic strategy except that it helps you migrate to this new folder structure if you already have some live code at the root of your deployment path.

![First deploy strategy diagram](https://user-images.githubusercontent.com/3642397/38944304-d6215ca0-4333-11e8-9768-ed562e061ca0.png)

## Tasks specific to the local strategy

### Firstdeploy:shared

If some of your shared files or directories are included in your `.gitignore`, the native `deploy:shared` task will not be able to retrieve them from your repository. This is typically the case for our `.env` file and the `storage` folder. If you already have a `.env` file and a `storage` folder in your live deployment path, you might want to use them in your shared folder. This is exactly what the `firstdeploy:shared` task does.

![firstdeploy shared diagram](https://user-images.githubusercontent.com/3642397/39003536-54c06930-43fb-11e8-8dd8-69a15d321db5.png)


### Firstdeploy:cleanup

At this point, your application has been deployed and your "gitignored" shared files have been resolved from your previously live code. Therefore all you need to do is remove all folder that are not deployer folders from your deployment path. This is exactly what the `firstdeploy:cleanup` task does.

:warning: Before removing all previously live code, **make sure your server's root path points to the `current` symlink**. For example if your `deploy_path` is `var/www/acme`, your server configurations should point to `var/www/acme/current`.

![firstdeploy cleanup diagram](https://user-images.githubusercontent.com/3642397/39003535-549d6264-43fb-11e8-9f08-553b03f92cd9.png)

During the deployment flow, when the `firstdeploy:cleanup` task is executed, you will see a warning message showing exactly what will be deleted and asking you for confimation.

```txt
➤ Executing task firstdeploy:cleanup

|--------------------------------------------------------
| [WARNING] You are about to delete some files
|--------------------------------------------------------
|
| You are about to delete all files and folders from your
| deployment path that are not deployer folders, that is:
| > `.dep`, `current`, `release`, `releases` and `shared`
| Make sure your server points to the "/current" symlink.

Deleting: <List of all files and folders to be deleted>
From directory: /var/www/acme

Are you sure you want to continue and delete those elements? [y/N]
```
