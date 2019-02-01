<?php

namespace Clue\GraphComposer\Exclusion\Dependency;

use JMS\Composer\Graph\DependencyEdge;

class NoDevDependencyRule implements DependencyRule
{
    public function isExcluded(DependencyEdge $dependency)
    {
        if ($dependency->isDevDependency()) {
            return true;
        }

        return false;
    }
}
