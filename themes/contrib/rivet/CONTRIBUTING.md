CONTRIBUTING
------------

You may setup your local environment with [Ddev]. This project leverages the
[Ddev Drupal Contrib] plugin.

1.  [Install Ddev] with a [Docker provider].
2.  Clone this project's repository from Drupal's GitLab.

        git clone git@git.drupal.org:project/rivet.git
        cd rivet

3.  Startup Ddev.

        ddev start

4.  Install composer dependencies.

        ddev poser

    Note: `ddev poser` is shorthand for `ddev composer` to add in Drupal core
    dependencies without needing to modify the root composer.json. Find out
    more in Ddev Drupal Contrib [commands].

5.  Install Drupal.

        ddev drush site:install

6.  Configure Drupal.

        ddev drush then rivet
        ddev drush config-set system.theme default rivet -y
        ddev drush en devel devel_generate
        ddev drush devel-generate:content

7.  Visit site in browser.

        ddev launch

    Or, login as user 1:

        ddev drush user:login

8.  Check code changes with phpcs, phpstan, stylelint, and eslint.

        ddev phpcs
        ddev phpstan
        ddev stylelint
        ddev eslint

    This is useful so you don't have to push and wait for Drupal GitLabCI build
    to show you failures.

9.  Push work to Merge Requests (MRs) opened via this project's [issue queue].


CHANGING DRUPAL CORE VERSION
----------------------------

Ddev Drupal Contrib installs a recent stable version of Drupal core via the `DRUPAL_CORE`
environment variable. Review .ddev/config.yaml to find the current default version.

Override the current default version of Drupal core by creating .ddev/config.local.yaml:

```yaml
web_environment:
    - DRUPAL_CORE=^9
```

[Ddev]: https://www.ddev.com/
[Ddev Drupal Contrib]: https://github.com/ddev/ddev-drupal-contrib
[Install Ddev]: https://ddev.readthedocs.io/en/stable/
[Docker provider]: https://ddev.readthedocs.io/en/stable/users/install/docker-installation/
[issue queue]: https://www.drupal.org/project/issues/rivet
[commands]: https://github.com/ddev/ddev-drupal-contrib#commands
