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
     * 
     * @param string $dir
     * @return \Fhaculty\Graph\Graph
     */
    public function createGraph()
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
        
            foreach ($package->getOutEdges() as $requires) {
                $targetName = $requires->getDestPackage()->getName();
                $target = $graph->createVertex($targetName, true);
                
                $label = $requires->getVersionConstraint();
                
                $edge = $start->createEdgeTo($target)->setLayout(array('label' => $label) + $this->layoutEdge);
                
                if ($requires->isDevDependency()) {
                    $edge->setLayout($this->layoutEdgeDev);
                }
            }
        }

        $graph->getVertex($this->dependencyGraph->getRootPackage()->getName())->setLayout($this->layoutVertexRoot);
        
        return $graph;
    }
        
    public function displayGraph()
    {
        $graph = $this->createGraph();
        
        $graphviz = new GraphViz($graph);
        $graphviz->setFormat($this->format);
        $graphviz->display();
    }
    
    public function getImagePath()
    {
        $graph = $this->createGraph();
        
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
