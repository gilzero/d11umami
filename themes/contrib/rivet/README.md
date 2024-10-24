# Rivet Design System theme for Drupal

The Rivet theme for Drupal is an implementation and adaptation of the
[Rivet Design System][] from Indiana University.

[Rivet Design System]: https://rivet.iu.edu


## Requirements

This module requires the [Layout Builder][] module from Drupal core to be enabled.

[Layout Builder]: https://www.drupal.org/docs/8/core/modules/layout-builder


## Installation

Install as you would normally install a contributed Drupal theme. For further
information, see [Installing Drupal Themes][].

[Installing Drupal Themes]: https://www.drupal.org/docs/extending-drupal/themes/installing-themes


## Configuration

1.  To test the theme, you may visit `/admin/appearance` and select
    "Enable and set as default".

1.  Alternatively, you may wish to create a sub-theme for your project by
    creating a theme and setting `base theme: rivet` in your sub-theme's
    info.yml file.


## Updating

The Rivet Base Theme was created for Drupal 10 using the new starterkit theme
generation. The theme will be maintained up-to-date with Drupal core's
`starterkit_theme` theme according to the documentation for
[Tracking upstream changes][].

[Tracking upstream changes]: https://www.drupal.org/docs/core-modules-and-themes/core-themes/starterkit-theme#s-tracking-upstream-changes


### Subtheme Generation

The recommended approach to using Rivet on any Drupal site is to create a subtheme.

Use the following command:

    php core/scripts/drupal generate-theme --name "Rivet Subtheme" --path themes/custom --starterkit rivet rivet_subtheme


## Maintainers

[//]: # cSpell:disable
[//]: # Do not add maintainers to cspell-project-words file

* James Wilson - [jwilson3](https://www.drupal.org/u/jwilson3)
