<?php

namespace Clue\GraphComposer\Exclusion\Package;

use JMS\Composer\Graph\PackageNode;

class ExcludeByNamePackageRule implements PackageRule
{
    /**
     * @var string
     */
    private $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public function isExcluded(PackageNode $package)
    {
        return preg_match($this->pattern, $package->getName());
    }
}
