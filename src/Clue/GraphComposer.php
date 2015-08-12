<?php

namespace Clue;

use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;

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

    private $format = 'svg';

    /**
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        $analyzer = new \JMS\Composer\DependencyAnalyzer();
        $this->dependencyGraph = $analyzer->analyze($dir);
    }

    /**
     * @param boolean $hideDevPackage
     * @return \Fhaculty\Graph\Graph
     */
    public function createGraph($hideDevPackage = false)
    {
        $graph = new Graph();

        foreach ($this->dependencyGraph->getPackages() as $package) {
            $name = $package->getName();
            $start = $graph->createVertex($name, true);

            $label = $name;
            if ($package->getVersion() !== null) {
                $label .= ': ' . $package->getVersion();
            }

            $start->setLayout(array('label' => $label) + $this->layoutVertex);

            $hasOnlyDevDependencies = true;
            foreach ($package->getOutEdges() as $requires) {
                if ($hideDevPackage && $requires->isDevDependency()) {
                    continue;
                }

                $hasOnlyDevDependencies = false;

                $targetName = $requires->getDestPackage()->getName();
                $target = $graph->createVertex($targetName, true);

                $label = $requires->getVersionConstraint();

                $edge = $start->createEdgeTo($target)->setLayout(array('label' => $label) + $this->layoutEdge);

                if ($requires->isDevDependency()) {
                    $edge->setLayout($this->layoutEdgeDev);
                }
            }

            if ($hideDevPackage && $hasOnlyDevDependencies) {
                $start->destroy();
            }
        }

        $graph->getVertex($this->dependencyGraph->getRootPackage()->getName())->setLayout($this->layoutVertexRoot);

        return $graph;
    }

    /**
     * @param boolean $hideDevPackage
     */
    public function displayGraph($hideDevPackage = false)
    {
        $graph = $this->createGraph($hideDevPackage);

        $graphviz = new GraphViz($graph);
        $graphviz->setFormat($this->format);
        $graphviz->display();
    }

    /**
     * @param boolean $hideDevPackage
     */
    public function getImagePath($hideDevPackage = false)
    {
        $graph = $this->createGraph($hideDevPackage);

        $graphviz = new GraphViz($graph);
        $graphviz->setFormat($this->format);

        return $graphviz->createImageFile();
    }

    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }
}
