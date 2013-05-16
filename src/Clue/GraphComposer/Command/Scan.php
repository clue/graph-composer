<?php

namespace Clue\GraphComposer\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Clue\GraphComposer;

class Scan extends Command
{
    protected function configure()
    {
        $this->setName('scan')
             ->setDescription('Scans the given directory and generates a graph image')
             ->addArgument('dir', InputArgument::OPTIONAL, 'Path to project directory to scan')
             ->addArgument('output', InputArgument::OPTIONAL, 'Path to output image file')
           /*->addOption('dev', null, InputOption::VALUE_NONE, 'If set, Whether require-dev dependencies should be shown') */;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // directory to scan
        $dir = $input->getArgument('dir');
        if ($dir === null) {
            $dir = '.';
        }
        
        $graph = new GraphComposer($dir);
        
        $output = $input->getArgument('output');
        if ($output !== null) {
            $graph->exportGraph($output);
        } else {
            $graph->displayGraph();
        }
    }
}