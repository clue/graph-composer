<?php

namespace Clue\GraphComposer\Exclusion\Dependency;

use JMS\Composer\Graph\DependencyEdge;

class NegateDependencyRule implements DependencyRule
{
    /**
     * @var DependencyRule
     */
    private $rule;

    public function __construct(DependencyRule $rule)
    {
        $this->rule = $rule;
    }

    public function isExcluded(DependencyEdge $dependency)
    {
        return !$this->rule->isExcluded($dependency);
    }
}
