<?php

namespace Clue\GraphComposer\Exclusion\Package;

use JMS\Composer\Graph\PackageNode;

class ExcludeTypePackageRule implements PackageRule
{
    /**
     * @var string
     */
    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function isExcluded(PackageNode $package)
    {
        $data = $package->getData();

        if (!isset($data['type'])) {
            return false;
        }

        return $data['type'] === $this->type;
    }
}
