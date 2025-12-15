# Outdated Config

Sometimes, in the process of making successive config changes, we need to 
maintain old config files so that outdated update hooks can still run
successfully.

When a new update runs into an error like:

> Configuration ______ depends on the  ____ configuration that will not exist
> after import.

we need to update that hook.

## Fixing an update hook

1. Find the version of the module where the failing update hook was committed.
2. Checkout or download the old version to a separate working directory.
3. Copy all config files that are being imported in that hook into a new
   directory, like `config/outdated/x.x.x`.
4. Change the `$path` line in the failing update hook, changing
   `/config/optional` to `/config/outdated/x.x.x`.
5. Commit those changes and test thoroughly. 