<?php
/**
 * Application documentation builder
 *
 * See https://github.com/fabpot/Sami
 *
 * To build doc, run:
 *     $ php src/vendor/sami/sami/sami.php render sami.config.php
 *
 * To update it, run:
 *     $ php src/vendor/sami/sami/sami.php update sami.config.php
 *
 */

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('vendor')
    ->exclude('templates')
    ->exclude('config')
    ->in(__DIR__.'/src')
;

$options = array(
    'title'                => 'WebDocBook',
    'build_dir'            => __DIR__.'/phpdoc',
    'cache_dir'            => __DIR__.'/../tmp/cache/webdocbook',
    'default_opened_level' => 1,
);

return new Sami($iterator, $options);

// Endfile
