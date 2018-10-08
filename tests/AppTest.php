<?php

use Clue\GraphComposer\App;

class AppTest extends PHPUnit_Framework_TestCase
{
    public function testVersionReturnsDev()
    {
        $app = new App();

        $this->assertEquals('@dev', $app->getVersion());
    }
}
