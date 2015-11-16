<?php

namespace Clue\GraphComposer\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Clue\GraphComposer\Graph\GraphComposer;

class Export extends Command
{
    protected function configure()
    {
        $this->setName('export')
             ->setDescription('Export dependency graph image for given project directory')
             ->addArgument('dir', InputArgument::OPTIONAL, 'Path to project directory to scan', '.')
             ->addArgument('output', InputArgument::OPTIONAL, 'Path to output image file')

             // add output format option. default value MUST NOT be given, because default is to overwrite with output extension
             ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Image format (svg, png, jpeg)'/*, 'svg'*/)

           /*->addOption('dev', null, InputOption::VALUE_NONE, 'If set, Whether require-dev dependencies should be shown') */;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $graph = new GraphComposer($input->getArgument('dir'));

        $target = $input->getArgument('output');
        if ($target !== null) {
            if (is_dir($target)) {
                $target = rtrim($target, '/') . '/graph-composer.svg';
            }

            $filename = basename($target);
            $pos = strrpos($filename, '.');
            if ($pos !== false && isset($filename[$pos + 1])) {
                // extension found and not empty
                $graph->setFormat(substr($filename, $pos + 1));
            }
        }

        $format = $input->getOption('format');
        if ($format !== null) {
            $graph->setFormat($format);
        }

        $path = $graph->getImagePath();

        if ($target !== null) {
            rename($path, $target);
        } else {
            readfile($path);
        }
    }
}
