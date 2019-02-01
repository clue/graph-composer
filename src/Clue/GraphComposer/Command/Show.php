<?php

namespace Clue\GraphComposer\Command;

use Clue\GraphComposer\Exclusion\Dependency\ChainedDependencyRule;
use Clue\GraphComposer\Exclusion\Dependency\NoDevDependencyRule;
use Clue\GraphComposer\Exclusion\Package\ChainedPackageRule;
use Clue\GraphComposer\Exclusion\Package\ExcludeByNamePackageRule;
use Clue\GraphComposer\Exclusion\Package\NoPhpExtensionRule;
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
            ->addOption('no-dev', null, InputOption::VALUE_NONE, 'Removes dev dependencies from the generated graph')
            ->addOption('no-php', null, InputOption::VALUE_NONE, 'Hides dependency to PHP')
            ->addOption('no-ext', null, InputOption::VALUE_NONE, 'Hide PHP extensions')
            ->addOption('depth', null, InputOption::VALUE_REQUIRED, 'Set the maximum depth of dependency graph', PHP_INT_MAX)
            ->addOption('exclude-regex', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Excludes packages using a regular expression like #^phpunit/.*/$#')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dependencyRule = new ChainedDependencyRule();
        if ($input->getOption('no-dev')) {
            $dependencyRule->add(new NoDevDependencyRule());
        }

        $packageRule = new ChainedPackageRule();
        if ($input->getOption('no-php')) {
            $packageRule->add(new ExcludeByNamePackageRule('#^php$#'));
        }

        if ($input->getOption('no-ext')) {
            $packageRule->add(new NoPhpExtensionRule());
        }

        foreach ($input->getOption('exclude-regex') as $regex) {
            $packageRule->add(new ExcludeByNamePackageRule($regex));
        }

        $graph = new GraphComposer(
            $input->getArgument('dir'),
            null,
            $packageRule,
            $dependencyRule,
            (int) $input->getOption('depth')
        );
        $graph->setFormat($input->getOption('format'));
        $graph->displayGraph();
    }
}
