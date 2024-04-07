<?php

declare(strict_types=1);

namespace Froq\AssetBundle\PimcoreAdminProvider;

use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class HighestVersionCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        return (string) $this->getValue($object);
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return (string) $this->getValue($object);
    }

    private function getValue(Concrete $object): int
    {
        if (!$object instanceof AssetResource) {
            throw new \InvalidArgumentException('Expected instance of AssetResource');
        }

        return AssetResourceHierarchyHelper::getHighestVersionNumber($object);
    }
}
