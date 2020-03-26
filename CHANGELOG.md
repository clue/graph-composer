# Changelog

## 1.1.0 (2020-03-26)

*   Feature: Forward compatibility with symfony/console v5, v4, v3 and legacy v2.
    (#34 by @keradus and #48 by @clue)

*   Feature / Fix: Update all dependencies and fix handling non-lowercase package names.
    (#50 and #52 by @clue)

*   Improve documentation and installation instructions and add support / sponsorship info.
    (#32 by @xavismeh and #43 and #49 by @clue)

*   Improve build setup, add clue/phar-composer to `require-dev`, add build script and update development docs.
    (#44 by @clue)

*   Improve test suite by adding PHPUnit to `require-dev`,
    support legacy PHP 5.3 through PHP 7.4 and legacy HHVM and simplify test matrix.
    (#42 and #51 by @clue)

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

