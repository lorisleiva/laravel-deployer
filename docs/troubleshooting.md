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