<?php

namespace Clue\GraphComposer;

use Symfony\Component\Console\Application as BaseApplication;

class App extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('clue/graph-composer', '@dev');

        $this->add(new Command\Show());
        $this->add(new Command\Export());
    }
}
