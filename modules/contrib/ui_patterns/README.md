# UI Patterns 2.x

Expose SDC components as Drupal plugins and use them seamlessly in Drupal development and site-building.

Components are reusable, nestable, guided by clear standards, and can be assembled together to build any number of applications. Examples: card, button, slider, pager, menu, toast...

## Project overview

The UI Patterns project provides 3 "toolset" modules:

- **UI Patterns**: the main module, based on Drupal Core SDC API, with additional powerful API and quality-of-life improvements
- **UI Patterns Library**: generates a pattern library page available at `/patterns`
  to be used as documentation for content editors or as a showcase for business. Use this module if you don't plan to
  use more advanced component library systems such as Storybook, PatternLab or Fractal.
  [Learn more](https://www.drupal.org/docs/contributed-modules/ui-patterns/define-your-patterns)
- **UI Patterns Legacy**: Load your UI Patterns 1.x components inside UI Patterns 2.x

4 "integration" modules:

- **UI Patterns Layouts**: allows to use components as layouts. This allows patterns to be used with Layout Builder,
  [Display Suite](https://www.drupal.org/project/ds) or [Panels](https://www.drupal.org/project/panels)
  out of the box. [Learn more](https://www.drupal.org/docs/contributed-modules/ui-patterns/use-patterns-as-layouts)
- **UI Patterns Blocks**: allows to use components as Blocks plugins.
- **UI Patterns Field Formatters**: allows to use components as Field Formatters plugins.
- **UI Patterns Views**: allows to use components as Views styles or Views rows plugins.
  [Learn more](https://www.drupal.org/docs/contributed-modules/ui-patterns/use-patterns-with-views)

1 "devel" module:

- **UI Patterns Devel**: provide some tools to help developers working with Component. Currently a Twig static
  validator to detect errors and help follow good practices for UI Patterns.

## Documentation

Documentation is available [here](https://www.drupal.org/docs/contributed-modules/ui-patterns).