<?php

use Clue\GraphComposer\Graph\GraphComposer;
use Graphp\GraphViz\GraphViz;
use Fhaculty\Graph\Graph;
use PHPUnit\Framework\TestCase;

class GraphVizMockDisplay extends GraphViz
{
    public $called = 0;
    public function display(Graph $graph)
    {
        ++$this->called;
    }
}

class GraphVizMockCreateImageFile extends GraphViz
{
    public $called = 0;
    public function createImageFile(Graph $graph)
    {
        return 'test' . ++$this->called . '.png';
    }
}

class GraphVizMockSetFormat extends GraphViz
{
    public $called = null;
    public function setFormat($format)
    {
        $this->called = $format;
    }
}

class GraphTest extends TestCase
{
    public function testCreateGraph()
    {
        $dir = __DIR__ . '/../';

        $graphComposer = new GraphComposer($dir);
        $graph = $graphComposer->createGraph();

        self::assertInstanceOf('Fhaculty\Graph\Graph', $graph);
        self::assertTrue(count($graph->getVertices()) > 0);
    }

    public function testDisplayGraphCallsDisplayGraphViz()
    {
        $dir = __DIR__ . '/../';

        // mocking with PHP 7.4 reports error with legacy PHPUnit, create manual mock classes instead
        $graphviz = new GraphVizMockDisplay();

        $graphComposer = new GraphComposer($dir, $graphviz);
        $graphComposer->displayGraph();

        self::assertEquals(1, $graphviz->called);
    }

    public function testGetImagePathWillCreateTemporaryImageFileViaGraphViz()
    {
        $dir = __DIR__ . '/../';

        // mocking with PHP 7.4 reports error with legacy PHPUnit, create manual mock classes instead
        $graphviz = new GraphVizMockCreateImageFile();

        $graphComposer = new GraphComposer($dir, $graphviz);
        $ret = $graphComposer->getImagePath();

        self::assertEquals('test1.png', $ret);
    }

    public function testSetFormatWillSetFormatOnGraphViz()
    {
        $dir = __DIR__ . '/../';

        // mocking with PHP 7.4 reports error with legacy PHPUnit, create manual mock classes instead
        $graphviz = new GraphVizMockSetFormat();

        $graphComposer = new GraphComposer($dir, $graphviz);
        $ret = $graphComposer->setFormat('gif');

        self::assertEquals($graphComposer, $ret);
        self::assertEquals('gif', $graphviz->called);
    }
}
