<?php

namespace Clue\GraphComposer\Graph;

use Fhaculty\Graph\Attribute\AttributeAware;
use Fhaculty\Graph\Attribute\AttributeBagNamespaced;
use Fhaculty\Graph\Graph;
use Graphp\GraphViz\GraphViz;

class GraphComposer
{
    /**
     * Regular dependency
     *
     * @var int
     */
    const DEPENDENCY = 1;
    /**
     * Development dependency
     *
     * @var int
     */
    const DEV_DEPENDENCY = 2;
    private $layoutVertex = array(
        'fillcolor' => '#eeeeee',
        'style' => 'filled, rounded',
        'shape' => 'box',
        'fontcolor' => '#314B5F'
    );
    private $layoutVertexRoot = array(
        'style' => 'filled, rounded, bold'
    );
    private $layoutEdge = array(
        'fontcolor' => '#767676',
        'fontsize' => 10,
        'color' => '#1A2833'
    );
    private $layoutEdgeDev = array(
        'style' => 'dashed'
    );
    private $dependencyGraph;
    /**
     * @var GraphViz
     */
    private $graphviz;

    /**
     *
     * @param string $dir
     * @param GraphViz|null $graphviz
     */
    public function __construct($dir, GraphViz $graphviz = null)
    {
        if ($graphviz === null) {
            $graphviz = new GraphViz();
            $graphviz->setFormat('svg');
        }
        $analyzer = new \JMS\Composer\DependencyAnalyzer();
        $this->dependencyGraph = $analyzer->analyze($dir);
        $this->graphviz = $graphviz;
    }

    /**
     * Displaay the graph
     *
     * @param int $filter Dependency type filter
     */
    public function displayGraph($filter = 0)
    {
        $graph = $this->createGraph($filter);
        $this->graphviz->display($graph);
    }

    /**
     * Create the graph
     *
     * @param int $filter Dependency type filter
     * @return Graph Graph
     */
    public function createGraph($filter = 0)
    {
        $graph = new Graph();
        $dependencies = $this->getDependencyList($filter);

        foreach ($this->dependencyGraph->getPackages() as $package) {
            $name = $package->getName();

            if (empty($dependencies[$name])) {
                continue;
            }

            $start = $graph->createVertex($name, true);

            $label = $name;
            if ($package->getVersion() !== null) {
                $label .= ': '.$package->getVersion();
            }

            $this->setLayout($start, array('label' => $label) + $this->layoutVertex);

            foreach ($package->getOutEdges() as $requires) {
                $targetName = $requires->getDestPackage()->getName();

                if (empty($dependencies[$targetName])) {
                    continue;
                }

                $target = $graph->createVertex($targetName, true);

                $label = $requires->getVersionConstraint();

                $edge = $start->createEdgeTo($target);
                $this->setLayout($edge, array('label' => $label) + $this->layoutEdge);

                if ($requires->isDevDependency()) {
                    $this->setLayout($edge, $this->layoutEdgeDev);
                }
            }
        }

        $root = $graph->getVertex($this->dependencyGraph->getRootPackage()->getName());
        $this->setLayout($root, $this->layoutVertexRoot);

        return $graph;
    }

    /**
     * Create a list of dependencies to show / export
     *
     * @param int $filter Dependency type filter
     * @return array Dependencies to show / export
     */
    protected function getDependencyList($filter = 0)
    {
        $allDependencies = array();

        // Run through all dependencies (including the root package)
        foreach ($this->dependencyGraph->getPackages() as $package) {
            $name = $package->getName();
            if (!array_key_exists($name, $allDependencies)) {
                $allDependencies[$name] = array();
            }

            // Run through all dependencies
            foreach ($package->getOutEdges() as $requires) {
                $targetName = $requires->getDestPackage()->getName();
                if (!array_key_exists($targetName, $allDependencies)) {
                    $allDependencies[$targetName] = array();
                }

                $allDependencies[$name][$targetName] = $requires->isDevDependency() ?
                    self::DEV_DEPENDENCY : self::DEPENDENCY;
            }
        }

        $rootPackage = $this->dependencyGraph->getRootPackage()->getName();
        $dependencies = array($rootPackage => 1);
        $this->filterDependencies($rootPackage, $filter, $allDependencies, $dependencies);
        return $dependencies;
    }

    /**
     * Filter the dependencies of a package
     *
     * @param string $package Package name
     * @param int $filter Dependency filter
     * @param array $dependencies All dependencies
     * @param array $filteredDependencies Filtered dependencies
     */
    protected function filterDependencies($package, $filter, array $dependencies, array &$filteredDependencies) {
        if (array_key_exists($package, $dependencies)) {
            foreach ($dependencies[$package] as $require => $dependency) {
                if ($dependency & $filter) {
                    $isRegistered = array_key_exists($require, $filteredDependencies);
                    if (!$isRegistered) {
                        $filteredDependencies[$require] = 1;
                        $this->filterDependencies(
                            $require,
                            $filter | self::DEPENDENCY,
                            $dependencies,
                            $filteredDependencies
                        );
                    } else {
                        ++$filteredDependencies[$require];
                    }
                }
            }
        }
    }

    protected function setLayout(AttributeAware $entity, array $layout)
    {
        $bag = new AttributeBagNamespaced($entity->getAttributeBag(), 'graphviz.');
        $bag->setAttributes($layout);

        return $entity;
    }

    /**
     * Get the graph image path
     *
     * @param int $filter Dependency type filter
     * @return string Graph image path
     */
    public function getImagePath($filter = 0)
    {
        $graph = $this->createGraph($filter);

        return $this->graphviz->createImageFile($graph);
    }

    public function setFormat($format)
    {
        $this->graphviz->setFormat($format);

        return $this;
    }
}
