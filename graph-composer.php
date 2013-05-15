<?php

require_once __DIR__ . '/vendor/autoload.php';

// directory to scan
$dir = isset($argv[1]) ? $argv[1] : __DIR__;

$graph = new Graph();
$graph->displayGraph($dir);
