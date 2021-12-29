<?php

use Clue\GraphComposer\App;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testVersionReturnsDev()
    {
        $app = new App();

        $this->assertEquals('@dev', $app->getVersion());
    }
}
