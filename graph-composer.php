<?php

use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;

require_once __DIR__ . '/vendor/autoload.php';

// directory to scan
$dir = isset($argv[1]) ? $argv[1] : __DIR__;

$analyzer = new \JMS\Composer\DependencyAnalyzer();
$dependencyGraph = $analyzer->analyze($dir);

$graph = new Graph();

foreach ($dependencyGraph->getPackages() as $package) {
    $name = $package->getName();
    $start = $graph->createVertex($name, true);
    $start->setLayout(array(
        'label' => $name . ': ' . $package->getVersion(),
        'fillcolor' => '#eeeeee',
        'style' => 'filled',
        'shape' => 'box'
    ));
    
    foreach ($package->getOutEdges() as $requires) {
        $targetName = $requires->getDestPackage()->getName();
        $target = $graph->createVertex($targetName, true);
        $start->createEdgeTo($target)->setLayout(array(
            'label' => $requires->getVersionConstraint(),
            'fontcolor' => '#999999',
            'fontsize' => 10
        ));
    }
}

$graph->getVertex($dependencyGraph->getRootPackage()->getName())->setLayout(array(
    'fontcolor' => 'red'
));

$graphviz = new GraphViz($graph);
$graphviz->setFormat('svg');
$graphviz->display();
