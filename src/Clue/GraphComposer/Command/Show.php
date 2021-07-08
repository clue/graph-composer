<?php

namespace Clue\GraphComposer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('show')
            ->setDescription('Show dependency graph image for given project directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $graph = $this->createGraph($input);
        $graph->displayGraph();

        return 0;
    }
}
