<?php

namespace Clue\GraphComposer\Exclusion\Package;

use JMS\Composer\Graph\PackageNode;

interface PackageRule
{
    public function isExcluded(PackageNode $package);
}
