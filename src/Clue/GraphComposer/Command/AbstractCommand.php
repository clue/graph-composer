<?php

namespace Clue\GraphComposer\Command;

use Clue\GraphComposer\Exclusion\Dependency\ChainedDependencyRule;
use Clue\GraphComposer\Exclusion\Dependency\NoDevDependencyRule;
use Clue\GraphComposer\Exclusion\Package\ChainedPackageRule;
use Clue\GraphComposer\Exclusion\Package\ExcludeByNamePackageRule;
use Clue\GraphComposer\Exclusion\Package\ExcludeTypePackageRule;
use Clue\GraphComposer\Exclusion\Package\NegatePackageRule;
use Clue\GraphComposer\Exclusion\Package\NoPhpExtensionRule;
use Clue\GraphComposer\Graph\GraphComposer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class AbstractCommand extends Command
{
    protected function configure()
    {
        $this
            ->addArgument('dir', InputArgument::OPTIONAL, 'Path to project directory to scan', '.')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Image format (svg, png, jpeg)', 'svg')
            ->addOption('no-dev', null, InputOption::VALUE_NONE, 'Removes dev dependencies from the generated graph')
            ->addOption('no-php', null, InputOption::VALUE_NONE, 'Hides dependency to PHP')
            ->addOption('no-ext', null, InputOption::VALUE_NONE, 'Hide PHP extensions')
            ->addOption('depth', null, InputOption::VALUE_REQUIRED, 'Set the maximum depth of dependency graph', PHP_INT_MAX)
            ->addOption('exclude-regex', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Excludes packages using a regular expression like #^phpunit/.*/$#')
            ->addOption('only-regex', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Includes only packages using a regular expression like #^phpunit/.*/$#')
            ->addOption('exclude-type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Excludes packages of given type.')
            ->addOption('only-type', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Includes only packages of given type.')
        ;
    }

    /**
     * @param InputInterface $input
     * @return GraphComposer
     */
    protected function createGraph(InputInterface $input)
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

        $onlyRegex = $input->getOption('only-regex');
        if (count($onlyRegex)) {
            $onlyRegexRule = new ChainedPackageRule();
            foreach ($onlyRegex as $regex) {
                $onlyRegexRule->add(new ExcludeByNamePackageRule($regex));
            }
            $packageRule->add(new NegatePackageRule($onlyRegexRule));
        }

        foreach ($input->getOption('exclude-type') as $type) {
            $packageRule->add(new ExcludeTypePackageRule($type));
        }

        $onlyTypes = $input->getOption('only-type');
        if (count($onlyTypes)) {
            $onlyTypesRule = new ChainedPackageRule();
            foreach ($onlyTypes as $type) {
                $onlyTypesRule->add(new ExcludeTypePackageRule($type));
            }
            $packageRule->add(new NegatePackageRule($onlyTypesRule));
        }

        $graph = new GraphComposer(
            $input->getArgument('dir'),
            null,
            $packageRule,
            $dependencyRule,
            (int)$input->getOption('depth')
        );
        $graph->setFormat($input->getOption('format'));

        return $graph;
    }
}
