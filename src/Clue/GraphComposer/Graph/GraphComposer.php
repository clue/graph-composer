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

    private $include = array();

    private $exclude = array();

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
     * @return \Fhaculty\Graph\Graph
     */
    public function createGraph()
    {
        $graph = new Graph();

        foreach ($this->dependencyGraph->getPackages() as $package) {
            $name = $package->getName();
            if ($this->isPackageFiltered($name)) {
                continue;
            }
            $start = $graph->createVertex($name, true);

            $label = $name;
            if ($package->getVersion() !== null) {
                $label .= ': ' . $package->getVersion();
            }

            $this->setLayout($start, array('label' => $label) + $this->layoutVertex);

            foreach ($package->getOutEdges() as $requires) {
                $targetName = $requires->getDestPackage()->getName();
                if ($this->isPackageFiltered($targetName)) {
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

    public function getImagePath()
    {
        $graph = $this->createGraph();

        return $this->graphviz->createImageFile($graph);
    }

    public function setFormat($format)
    {
        $this->graphviz->setFormat($format);

        return $this;
    }

    public function setInclude(array $include)
    {
      $this->include = array_map(array($this, 'packageToRegex'), $include);
    }

    public function setExclude(array $exclude)
    {
      $this->exclude = array_map(array($this, 'packageToRegex'), $exclude);
    }

    private function packageToRegex($packageDescription)
    {
      $parts = explode('/', $packageDescription);
      $parts = array_map(function ($part) { return $part === '*' ? '.*' : $part; }, $parts);
      return '/' . implode('\\/', $parts) . '/';
    }

    public function isPackageFiltered($packageName)
    {
      return (!empty($this->include) && !array_reduce($this->include, function ($acc, $cur) use ($packageName) { return $acc || (preg_match($cur, $packageName) === 1); }, false))
      || (!empty($this->exclude) && array_reduce($this->exclude, function ($acc, $cur) use ($packageName) { return $acc || (preg_match($cur, $packageName) === 1); }, false));
    }
}
