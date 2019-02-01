<?php

namespace Clue\GraphComposer\Exclusion\Dependency;

use JMS\Composer\Graph\DependencyEdge;

interface DependencyRule
{
    public function isExcluded(DependencyEdge $dependency);
}
