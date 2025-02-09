<?php

$config = new Amp\CodeStyle\Config();
$config->getFinder()->in(__DIR__ . '/src')->in(__DIR__ . '/tools');
$cacheDir = getenv('TRAVIS') ? getenv('HOME') . '/.php-cs-fixer' : __DIR__;
$config->setCacheFile($cacheDir . '/.php_cs.cache');
return $config;