<?php

use Clue\GraphComposer\Graph\GraphComposer;

class GraphTest extends PHPUnit_Framework_TestCase
{
    public function testCreateGraph()
    {
        $dir = __DIR__ . '/../';

        $graphComposer = new GraphComposer($dir);
        $graph = $graphComposer->createGraph();

        $this->assertInstanceOf('Fhaculty\Graph\Graph', $graph);
        $this->assertTrue(count($graph->getVertices()) > 0);
    }

    public function testWillDisplayGraph()
    {
        $dir = __DIR__ . '/../';

        $graphviz = $this->getMock('Graphp\GraphViz\GraphViz');
        $graphviz->expects($this->once())->method('display');

        $graphComposer = new GraphComposer($dir, $graphviz);
        $graphComposer->displayGraph();
    }

    public function testWillWriteTemporaryGraph()
    {
        $dir = __DIR__ . '/../';

        $graphviz = $this->getMock('Graphp\GraphViz\GraphViz');
        $graphviz->expects($this->once())->method('createImageFile')->will($this->returnValue('test.png'));

        $graphComposer = new GraphComposer($dir, $graphviz);
        $ret = $graphComposer->getImagePath();

        $this->assertEquals('test.png', $ret);
    }

    public function testWillSetFormat()
    {
        $dir = __DIR__ . '/../';

        $graphviz = $this->getMock('Graphp\GraphViz\GraphViz');
        $graphviz->expects($this->once())->method('setFormat')->with($this->equalTo('gif'));

        $graphComposer = new GraphComposer($dir, $graphviz);
        $ret = $graphComposer->setFormat('gif');

        $this->assertEquals($graphComposer, $ret);
    }

    public function testWillNotRestrictWithoutSetting()
    {
        $dir = __DIR__ . '/../';

        $graphComposer = new GraphComposer($dir);

        $this->assertTrue(!$graphComposer->isPackageFiltered('c/x'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('c/y'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('b/y'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('a/x'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('b/x'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('a/y'));
    }

    public function testWillInclude()
    {
        $dir = __DIR__ . '/../';

        $graphComposer = new GraphComposer($dir);

        $included = [
          'a/*',
          'b/x'
        ];

        $graphComposer->setInclude($included);

        $this->assertTrue($graphComposer->isPackageFiltered('c/x'));
        $this->assertTrue($graphComposer->isPackageFiltered('c/y'));
        $this->assertTrue($graphComposer->isPackageFiltered('b/y'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('a/x'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('b/x'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('a/y'));
    }

    public function testWillExclude()
    {
        $dir = __DIR__ . '/../';

        $graphComposer = new GraphComposer($dir);

        $excluded = [
          'a/*',
          'b/x'
        ];

        $graphComposer->setExclude($excluded);

        $this->assertTrue(!$graphComposer->isPackageFiltered('c/x'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('c/y'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('b/y'));
        $this->assertTrue($graphComposer->isPackageFiltered('a/x'));
        $this->assertTrue($graphComposer->isPackageFiltered('b/x'));
        $this->assertTrue($graphComposer->isPackageFiltered('a/y'));
    }

    public function testWillIncludeAndExclude()
    {
        $dir = __DIR__ . '/../';

        $graphComposer = new GraphComposer($dir);

        $included = [
          'a/*',
          'b/x'
        ];

        $excluded = [
          '*/y'
        ];

        $graphComposer->setInclude($included);
        $graphComposer->setExclude($excluded);

        $this->assertTrue($graphComposer->isPackageFiltered('c/x'));
        $this->assertTrue($graphComposer->isPackageFiltered('c/y'));
        $this->assertTrue($graphComposer->isPackageFiltered('b/y'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('a/x'));
        $this->assertTrue(!$graphComposer->isPackageFiltered('b/x'));
        $this->assertTrue($graphComposer->isPackageFiltered('a/y'));
    }
}
