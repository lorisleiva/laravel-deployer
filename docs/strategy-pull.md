# Git pull only strategy (no zero downtime)

This strategy provides a very quick way to deploy your application by simply running `git pull` in your current folder. It updates directly the `current` folder and does not create a new release.

:warning: **Warning:**
* This strategy does not provide zero-downtime deployments. Use only for small changes that need to be live asap. 
* Futhermore this strategy will not run any tasks in your `build` hook to avoid spending too much time in "no zero-downtime" land.

![Git pull only strategy diagram](https://user-images.githubusercontent.com/3642397/39048056-7d4a3ec0-449c-11e8-8479-5b542280f73b.png)

## Tasks specific to the pull strategy

### pull:update_code

Simply goes to your `current` directory and run `git pull`. It throws an error message if no `current` directory exists, advising you to first deploy your application using another strategy.