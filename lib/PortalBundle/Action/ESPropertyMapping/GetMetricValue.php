<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action\ESPropertyMapping;

use Froq\PortalBundle\Enum\MetricUnits;
use Pimcore\Model\DataObject\Data\QuantityValue;

final class GetMetricValue
{
    public function __invoke(QuantityValue $quantityValue, string $metricUnit): float|int|string|null
    {
        return match ($metricUnit) {
            MetricUnits::Millilitre->readable() => (
                fn () => $quantityValue->getUnit()?->getId() === MetricUnits::Litre->readable() ?
                    (float) $quantityValue->getUnit()->getFactor() * (float) $quantityValue->getValue()
                    : $quantityValue->getValue()
            )(),

            MetricUnits::Grams->readable() => (
                fn () => $quantityValue->getUnit()?->getId() === MetricUnits::Kilograms->readable() ?
                    (float) $quantityValue->getUnit()->getFactor() * (float) $quantityValue->getValue()
                    : $quantityValue->getValue()
            )(),

            MetricUnits::Pieces->readable(), MetricUnits::Each->readable() => (
                fn () => $quantityValue->getUnit()?->getId() === MetricUnits::Pieces->readable() ||
                    $quantityValue->getUnit()?->getId() === MetricUnits::Each->readable() ?
                    $quantityValue->getValue() : ''

            )(),

            default => $quantityValue->getValue()
        };
    }
}
