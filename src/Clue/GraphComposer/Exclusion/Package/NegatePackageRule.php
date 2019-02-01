<?php

namespace Clue\GraphComposer\Exclusion\Package;

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
