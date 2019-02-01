# Changelog

## ?.?.? (unreleased)

* Dev dependencies are only rendered for the root package

* Added new options to filter packages and dependencies:
    * `--no-dev` will hide dev dependencies
    * `--no-php` will hide the constraints regarding the PHP version
    * `--no-ext` will hide PHP extensions
    * `--depth` will limit the depth of the generated graph
    * `--exclude-regex` allows to apply regular expressions to hide packages
    * `--only-regex` show only packages with their name matching the expression
    * `--exclude-type` exclude pacakges with given type
    * `--only-type` only show packages of given type

## 1.0.0 (2015-11-17)

*   First stable release, now following SemVer.

*   Feature: Can now be installed as a `require-dev` Composer dependency and
    supports running as `./vendor/bin/graph-composer`.
    (#12 by @elkuku)
    
*   Fix: Update dependencies in order to improve error reporting and
    MS Windows support.
    (#23 by @clue)

*   Updated documentation, test suite and project structure.
    (#18, #16 by @nubs and #24, #25, #26, #27 by @clue)

## 0.1.1 (2013-09-11)

* Update jms/composer-deps-analyzer to v0.1.0 and clue/graph to v0.7.0
* Fix: Opening graph images now also works on Mac OS X

## 0.1.0 (2013-05-17)

* BC break: Whole new command line interface
* Feature: Proper command line arguments and help
* Feature: Image format can now be selected (svg, png, jpg/jpeg, etc.)

## 0.0.2 (2013-05-15)

* Feature: Add option to export graph images

## 0.0.1 (2013-05-15)

* First tagged release

