<?php

namespace Clue\GraphComposer\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Clue\GraphComposer;

class Export extends Command
{
    protected function configure()
    {
        $this->setName('export')
             ->setDescription('Export dependency graph image for given project directory')
             ->addArgument('dir', InputArgument::OPTIONAL, 'Path to project directory to scan', '.')
             ->addArgument('output', InputArgument::OPTIONAL, 'Path to output image file')
           /*->addOption('dev', null, InputOption::VALUE_NONE, 'If set, Whether require-dev dependencies should be shown') */;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $graph = new GraphComposer($input->getArgument('dir'));
        
        $output = $input->getArgument('output');
        if ($output !== null) {
            $graph->exportGraph($output);
        } else {
            $path = $graph->getImagePath();
            
            readfile($path);
        }
    }
}