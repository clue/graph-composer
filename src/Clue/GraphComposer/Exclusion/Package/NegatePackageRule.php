<?php

namespace Clue\GraphComposer\Exclusion\Dependency;

use Clue\GraphComposer\Exclusion\Package\PackageRule;
use JMS\Composer\Graph\PackageNode;

class NegatePackageRule implements PackageRule
{
    /**
     * @var DependencyRule
     */
    private $rule;

    public function __construct(PackageRule $rule)
    {
        $this->rule = $rule;
    }

    public function isExcluded(PackageNode $packageNode)
    {
        return !$this->rule->isExcluded($packageNode);
    }
}
