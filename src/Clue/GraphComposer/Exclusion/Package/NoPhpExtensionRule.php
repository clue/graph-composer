<?php

namespace Clue\GraphComposer\Exclusion\Package;

use JMS\Composer\Graph\PackageNode;

class NoPhpExtensionRule implements PackageRule
{
    public function isExcluded(PackageNode $package)
    {
        if ($package->isPhpExtension()) {
            return true;
        }

        return false;
    }
}
