UPDATING DEPENDENCIES
---------------------

This project depends on 3rd party PHP libraries. It also specifies suggested "dev dependencies"
for contribution on local development environments. Occasionally, Ddev and Ddev Drupal Contrib
must be updated as well.

1.  Create an issue, MR, and checkout the MR branch.
2.  Update Ddev and Ddev Drupal Contrib itself.

    Read https://ddev.readthedocs.io/en/stable/users/install/ddev-upgrade/

        ddev config --update
        ddev get ddev/ddev-drupal-contrib

3. Manually review and revert hunks that replace _module_ path with _theme_ path.

4. Restart ddev.

        ddev restart
        ddev poser
        ddev symlink-project

5.  Review and update PHP dependencies defined in composer.json

        ddev composer outdated --direct

6.  (Optionally) update the nodejs version in .ddev/config.yml to latest stable.

7.  Check for outdated npm dependencies, and update them.

        ddev npm outdated

        ddev npm update --save-dev

8.  Test clean install, commit, and push.
