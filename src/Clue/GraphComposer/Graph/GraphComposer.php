<?php

namespace Clue\GraphComposer\Graph;

use Clue\GraphComposer\Exclusion\Dependency\ChainedDependencyRule;
use Clue\GraphComposer\Exclusion\Dependency\DependencyRule;
use Clue\GraphComposer\Exclusion\Package\ChainedPackageRule;
use Clue\GraphComposer\Exclusion\Package\PackageRule;
use Fhaculty\Graph\Attribute\AttributeAware;
use Fhaculty\Graph\Attribute\AttributeBagNamespaced;
use Fhaculty\Graph\Graph;
use Graphp\GraphViz\GraphViz;
use JMS\Composer\Graph\PackageNode;

class GraphComposer
{
    private $layoutVertex = array(
        'fillcolor' => '#eeeeee',
        'style' => 'filled, rounded',
        'shape' => 'box',
        'fontcolor' => '#314B5F',
    );

    private $layoutVertexRoot = array(
        'style' => 'filled, rounded, bold',
    );

    private $layoutEdge = array(
        'fontcolor' => '#767676',
        'fontsize' => 10,
        'color' => '#1A2833',
    );

    private $layoutEdgeDev = array(
        'style' => 'dashed',
        'fontcolor' => '#767676',
        'fontsize' => 10,
        'color' => '#1A2833',
    );

    private $dependencyGraph;

    /**
     * @var GraphViz
     */
    private $graphviz;

    /**
     * The maximum depth of dependency to display.
     *
     * @var int
     */
    private $maxDepth;

    /**
     * @var PackageRule
     */
    private $packageExclusionRule;

    /**
     * @var DependencyRule
     */
    private $dependencyExclusionRule;

    public function __construct(
        $dir,
        GraphViz $graphviz = null,
        PackageRule $packageExclusionRule = null,
        DependencyRule $dependencyExclusionRule = null,
        $maxDepth = PHP_INT_MAX
    ) {
        if ($graphviz === null) {
            $graphviz = new GraphViz();
            $graphviz->setFormat('svg');
        }

        if ($packageExclusionRule === null) {
            $packageExclusionRule = new ChainedPackageRule();
            //$packageExclusionRule->add(new NoPhpExtensionRule());
            //$packageExclusionRule->add(new ExcludeByNamePackageRule('#^php$#'));
        }

        if ($dependencyExclusionRule === null) {
            $dependencyExclusionRule = new ChainedDependencyRule();
            // $dependencyExclusionRule->add(new NoDevDependencyRule());
        }

        $analyzer = new \JMS\Composer\DependencyAnalyzer();
        $this->dependencyGraph = $analyzer->analyze($dir);
        $this->graphviz = $graphviz;
        $this->packageExclusionRule = $packageExclusionRule;
        $this->dependencyExclusionRule = $dependencyExclusionRule;
        $this->maxDepth = $maxDepth;
    }

    /**
     * @return \Fhaculty\Graph\Graph
     */
    public function createGraph()
    {
        $graph = new Graph();

        $drawnPackages = array();
        $rootPackage = $this->dependencyGraph->getRootPackage();
        $this->drawPackageNode($graph, $rootPackage, $drawnPackages, $this->layoutVertexRoot);

        return $graph;
    }

    public function displayGraph()
    {
        $graph = $this->createGraph();

        $this->graphviz->display($graph);
    }

    public function getImagePath()
    {
        $graph = $this->createGraph();

        return $this->graphviz->createImageFile($graph);
    }

    private function drawPackageNode(
        Graph $graph,
        PackageNode $packageNode,
        array &$drawnPackages,
        array $layoutVertex = null,
        $depth = 0
    ) {
        // the root package may not excluded
        // beginning with $depth = 1 the packages are filtered using the exclude rule
        if ($depth > 0 && $this->packageExclusionRule->isExcluded($packageNode)) {
            return null;
        }

        $name = $packageNode->getName();
        // ensure that packages are only drawn once
        // if two packages in the tree require a package twice
        // then this dependency does not need to be drawn twice
        // and the vertex is returned directly (so an edge can be added)
        if (isset($drawnPackages[$name])) {
            return $drawnPackages[$name];
        }

        if ($depth > $this->maxDepth) {
            return null;
        }

        if ($layoutVertex === null) {
            $layoutVertex = $this->layoutVertex;
        }

        $vertex = $drawnPackages[$name] = $graph->createVertex($name, true);

        $label = $name;
        if ($packageNode->getVersion()) {
            $label .= ': ' .$packageNode->getVersion();
        }
        $this->setLayout($vertex, array('label' => $label) + $layoutVertex);

        // this foreach will loop over the dependencies of the current package
        foreach ($packageNode->getOutEdges() as $dependency) {
            if ($this->dependencyExclusionRule->isExcluded($dependency)) {
                continue;
            }

            // never show dev dependencies of dependencies:
            // they are not relevant for the current application and are ignored by composer
            if ($depth > 0 && $dependency->isDevDependency()) {
                continue;
            }

            $targetVertex = $this->drawPackageNode($graph, $dependency->getDestPackage(), $drawnPackages, null, $depth + 1);

            // drawPackageNode will return null if the package should not be shown
            // also the dependencies of a package will be only drawn if max depth is not reached
            // this ensures that packages in a deeper level will not have any dependency
            if ($targetVertex && $depth < $this->maxDepth) {
                $label = $dependency->getVersionConstraint();
                $edge = $vertex->createEdgeTo($targetVertex);
                $layoutEdge = $dependency->isDevDependency() ? $this->layoutEdgeDev : $this->layoutEdge;
                $this->setLayout($edge, array('label' => $label) + $layoutEdge);
            }
        }

        return $vertex;
    }

    private function setLayout(AttributeAware $entity, array $layout)
    {
        $bag = new AttributeBagNamespaced($entity->getAttributeBag(), 'graphviz.');
        $bag->setAttributes($layout);

        return $entity;
    }

    public function setFormat($format)
    {
        $this->graphviz->setFormat($format);

        return $this;
    }
}
