/**
 * @file
 * Contains the lint:css task for acquia_claro.
 */

/* global module */

module.exports = function (gulp, plugins, options) {
  'use strict';

  // Lint scss files.
  gulp.task('lint:css', function lintCSS () {
    const gulpStylelint = require('@ronilaukkarinen/gulp-stylelint');
    return gulp.src(options.sass.files)
      .pipe(plugins.plumber())
      .pipe(gulpStylelint({
        reporters: [
          {
            formatter: 'string',
            console: true
          }
        ]
      }))
      .pipe(plugins.plumber.stop());
  });

  // Lint scss files and throw an error for a CI to catch.
  gulp.task('lint:css-with-fail', function lintWithFail () {
    const gulpStylelint = require('@ronilaukkarinen/gulp-stylelint');
    return gulp.src(options.sass.files)
      .pipe(plugins.plumber())
      .pipe(gulpStylelint({
        reporters: [
          {
            formatter: 'string',
            console: true,
            failAfterError: true
          }
        ]
      }));
  });
};
