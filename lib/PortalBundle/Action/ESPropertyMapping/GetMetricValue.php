<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action\ESPropertyMapping;

use Froq\PortalBundle\Enum\MetricUnits;
use Pimcore\Model\DataObject\Data\QuantityValue;

final class GetMetricValue
{
    public function __invoke(QuantityValue $quantityValue, string $metricUnit): string
    {
        return match ($metricUnit) {
            MetricUnits::Millilitre->readable(),
            MetricUnits::Grams->readable() => $this->getConvertedValueWithMetricUnit($quantityValue, $metricUnit),

            MetricUnits::Pieces->readable() => $this->getPiecesEachStringValue($quantityValue),

            default => ''
        };
    }

    private function getConvertedValueWithMetricUnit(QuantityValue $quantityValue, string $metricUnit): string
    {
        $result = $quantityValue->getUnit()?->getFactor() * (float) $quantityValue->getValue();

        return match ($quantityValue->getUnit()?->getId()) {
            MetricUnits::Millilitre->readable(),
            MetricUnits::Litre->readable(),
            MetricUnits::Centilitre->readable() =>
                $metricUnit === MetricUnits::Millilitre->readable() ? $result.' '.MetricUnits::Millilitre->readable() : '',

            MetricUnits::Grams->readable(),
            MetricUnits::Kilograms->readable(),
            MetricUnits::Milligrams->readable() =>
                $metricUnit === MetricUnits::Grams->readable() ? $result.' '.MetricUnits::Grams->readable() : '',

            default => ''
        };
    }

    private function getPiecesEachStringValue(QuantityValue $quantityValue): string
    {
        return match ($quantityValue->getUnit()?->getId()) {
            MetricUnits::Pieces->readable(),
            MetricUnits::Each->readable() => $quantityValue->getValue().' '.$quantityValue->getUnit()->getId(),

            default => ''
        };
    }
}
