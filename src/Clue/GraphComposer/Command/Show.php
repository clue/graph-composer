<?php

namespace Clue\GraphComposer\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Clue\GraphComposer;

class Show extends Command
{
    protected function configure()
    {
        $this->setName('show')
             ->setDescription('Show dependency graph image for given project directory')
             ->addArgument('dir', InputArgument::OPTIONAL, 'Path to project directory to scan', '.')
             ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Image format (svg, png, jpeg)', 'svg')
             ->addOption('no-dev', null, InputOption::VALUE_NONE, 'Hide dev dependencies.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $graph = new GraphComposer($input->getArgument('dir'));
        $graph->setFormat($input->getOption('format'));
        $hideDevPackage = $input->getOption('no-dev');
        $graph->displayGraph($hideDevPackage);
    }
}
