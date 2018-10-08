<?php

// explicitly give VERSION via ENV or ask git for current version
$version = getenv('VERSION');
if ($version === false) {
    $version = ltrim(exec('git describe --always --dirty', $_, $code), 'v');
    if ($code !== 0) {
        fwrite(STDERR, 'Error: Unable to get version info from git. Try passing VERSION via ENV' . PHP_EOL);
        exit(1);
    }
}

// use first argument as output file or use "graph-composer-{version}.phar"
$out = isset($argv[1]) ? $argv[1] : ('graph-composer-' . $version . '.phar');

passthru('
rm -rf build && mkdir build &&
cp -r bin/ src/ composer.json composer.lock LICENSE build/ &&
sed -i \'s/@dev/' . $version .'/g\' build/src/Clue/GraphComposer/App.php &&
composer install -d build/ --no-dev &&

cd build/ && rm -rf composer.lock vendor/*/*/tests/ vendor/*/*/*.md vendor/*/*/composer.* vendor/*/*/phpunit.* && cd .. &&
cd build/vendor/symfony/console/Symfony/Component/Console/ && rm -rf Tests/ *.md composer.* phpunit.* && cd - &&
vendor/bin/phar-composer build build/ ' . escapeshellarg($out) . ' &&

php ' . escapeshellarg($out) . ' --version', $code);
exit($code);
