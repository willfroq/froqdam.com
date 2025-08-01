<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Froq\PortalBundle\Opensearch\Enum\MetricUnits;
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
        $conversionFactor = $quantityValue->getUnit()?->getFactor() ?: 1;
        $converted = $conversionFactor * (float) $quantityValue->getValue();

        return match ($metricUnit) {
            MetricUnits::Millilitre->readable(),
            MetricUnits::Litre->readable(),
            MetricUnits::Centilitre->readable() =>
                $metricUnit === MetricUnits::Millilitre->readable() ? $converted.' '.$metricUnit : '',

            MetricUnits::Grams->readable(),
            MetricUnits::Kilograms->readable(),
            MetricUnits::Milligrams->readable() =>
                $metricUnit === MetricUnits::Grams->readable() ? $converted.' '.$metricUnit : '',

            default => ''
        };
    }

    private function getPiecesEachStringValue(QuantityValue $quantityValue): string
    {
        return match ($quantityValue->getUnit()?->getAbbreviation()) {
            MetricUnits::Pieces->readable(),
            MetricUnits::Each->readable() => $quantityValue->getValue().' '.MetricUnits::Pieces->readable(),

            default => ''
        };
    }
}
