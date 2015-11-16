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
        $this->assertTrue(count($graph->getVertices()) > 0);
    }

    public function testWillDisplayGraph()
    {
        $dir = __DIR__ . '/../';

        $graphviz = $this->getMock('Graphp\GraphViz\GraphViz');
        $graphviz->expects($this->once())->method('display');

        $graphComposer = new Clue\GraphComposer($dir, $graphviz);
        $graphComposer->displayGraph();
    }

    public function testWillWriteTemporaryGraph()
    {
        $dir = __DIR__ . '/../';

        $graphviz = $this->getMock('Graphp\GraphViz\GraphViz');
        $graphviz->expects($this->once())->method('createImageFile')->will($this->returnValue('test.png'));

        $graphComposer = new Clue\GraphComposer($dir, $graphviz);
        $ret = $graphComposer->getImagePath();

        $this->assertEquals('test.png', $ret);
    }

    public function testWillSetFormat()
    {
        $dir = __DIR__ . '/../';

        $graphviz = $this->getMock('Graphp\GraphViz\GraphViz');
        $graphviz->expects($this->once())->method('setFormat')->with($this->equalTo('gif'));

        $graphComposer = new Clue\GraphComposer($dir, $graphviz);
        $ret = $graphComposer->setFormat('gif');

        $this->assertEquals($graphComposer, $ret);
    }
}
