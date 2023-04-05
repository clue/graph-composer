<?php

namespace Clue\GraphComposer\Graph;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Attribute\AttributeAware;
use Fhaculty\Graph\Attribute\AttributeBagNamespaced;
use Graphp\GraphViz\GraphViz;

class GraphComposer
{
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
     *
     * @param string $dir
     * @param array $filters
     * @return \Fhaculty\Graph\Graph
     */
    public function createGraph($filters = array())
    {
        $graph = new Graph();

        $vendors = isset($filters['vendors']) ? explode(',', $filters['vendors']) : null;

        /** @var \JMS\Composer\Graph\PackageNode $package */
        foreach ($this->dependencyGraph->getPackages() as $package) {

            if ($this->filterByVendors($package, $vendors)) continue;

            $name = $package->getName();
            $start = $graph->createVertex($name, true);

            $label = $name;
            if ($package->getVersion() !== null) {
                $label .= ': ' . $package->getVersion();
            }

            $this->setLayout($start, array('label' => $label) + $this->layoutVertex);

            foreach ($package->getOutEdges() as $requires) {
                $targetName = $requires->getDestPackage()->getName();
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

    private function setLayout(AttributeAware $entity, array $layout)
    {
        $bag = new AttributeBagNamespaced($entity->getAttributeBag(), 'graphviz.');
        $bag->setAttributes($layout);

        return $entity;
    }

    public function displayGraph()
    {
        $graph = $this->createGraph();

        $this->graphviz->display($graph);
    }

    public function getImagePath($filters = array())
    {
        $graph = $this->createGraph($filters);

        return $this->graphviz->createImageFile($graph);
    }

    public function setFormat($format)
    {
        $this->graphviz->setFormat($format);

        return $this;
    }

    protected function filterByVendors(\JMS\Composer\Graph\PackageNode $package, array $vendors = null)
    {
        $packageName = $package->getName();

        if (strpos($packageName, '/') !== false) {
            $vendorName = strstr($packageName, '/', true);
        } else {
            // if the package name has "/" in it, everything before "/" should be considered as vendor,
            // if not - the whole name should be considered as vendor.
            $vendorName = $packageName;
        }

        if (!is_null($vendors) && !in_array($vendorName, $vendors)) {
            return true;
        }
        return false;
    }
}
