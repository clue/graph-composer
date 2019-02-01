<?php

namespace Clue\GraphComposer\Exclusion\Package;

use JMS\Composer\Graph\PackageNode;

class ChainedPackageRule implements PackageRule
{
    /**
     * @var PackageRule[]
     */
    private $rules = array();

    public function add(PackageRule $rule)
    {
        $this->rules[] = $rule;
    }

    public function isExcluded(PackageNode $packageNode)
    {
        foreach ($this->rules as $rule) {
            if ($rule->isExcluded($packageNode)) {
                return true;
            }
        }

        return false;
    }
}
