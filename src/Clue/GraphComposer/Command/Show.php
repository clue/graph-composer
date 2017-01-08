<?php

namespace Clue\GraphComposer\Command;

use Clue\GraphComposer\Graph\GraphComposer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends Command
{
    protected function configure()
    {
        $this->setName('show')
            ->setDescription('Show dependency graph image for given project directory')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Path to project directory to scan', '.')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Image format (svg, png, jpeg)', 'svg')
            ->addOption('no-dev', null, InputOption::VALUE_NONE, 'Hide development dependencies')
            ->addOption('dev-only', null, InputOption::VALUE_NONE, 'Show development dependencies only');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getOption('dev-only') ?
            GraphComposer::DEV_DEPENDENCY :
            (
                $input->getOption('no-dev') ?
                    GraphComposer::DEPENDENCY :
                    (GraphComposer::DEPENDENCY | GraphComposer::DEV_DEPENDENCY)
            );
        $graph = new GraphComposer($input->getArgument('dir'));
        $graph->setFormat($input->getOption('format'));
        $graph->displayGraph($filter);
    }
}
