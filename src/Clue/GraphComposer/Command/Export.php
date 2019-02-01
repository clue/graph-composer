<?php

namespace Clue\GraphComposer\Command;

use Clue\GraphComposer\Graph\GraphComposer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Export extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('export')
             ->setDescription('Export dependency graph image for given project directory')
             ->addArgument('output', InputArgument::OPTIONAL, 'Path to output image file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $graph = $this->createGraph($input);

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

        $path = $graph->getImagePath();

        if ($target !== null) {
            rename($path, $target);
        } else {
            readfile($path);
        }
    }
}
