<?php

require __DIR__ . '/../vendor/autoload.php';

class GraphTest extends PHPUnit_Framework_TestCase
{
    public function testCreateGraph()
    {
        $dir = __DIR__ . '/../';
        
        $graphComposer = new Clue\GraphComposer($dir);
        $graph = $graphComposer->createGraph();
        
        $this->assertInstanceOf('Fhaculty\Graph\Graph', $graph);
        $this->assertTrue($graph->getNumberOfVertices() > 0);
    }
}
