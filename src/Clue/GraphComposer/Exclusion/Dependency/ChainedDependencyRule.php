<?php

namespace Clue\GraphComposer\Exclusion\Dependency;

use JMS\Composer\Graph\DependencyEdge;

class ChainedDependencyRule implements DependencyRule
{
    /**
     * @var DependencyRule[]
     */
    private $rules = array();

    public function add(DependencyRule $rule)
    {
        $this->rules[] = $rule;
    }

    public function isExcluded(DependencyEdge $dependency)
    {
        foreach ($this->rules as $rule) {
            if ($rule->isExcluded($dependency)) {
                return true;
            }
        }

        return false;
    }
}
