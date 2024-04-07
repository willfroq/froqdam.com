<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO\FormData;

use Froq\PortalBundle\DTO\AggregationChoiceDto;

class FilterMetadataDto
{
    private string $fieldName = '';
    private string $type = '';

    /**
     * @var array<string|int, mixed>
     */
    private array $aggregationChoices = [];

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string|int, mixed>
     */
    public function getAggregationChoices(): array
    {
        return $this->aggregationChoices;
    }

    /**
     * @param array<int, AggregationChoiceDto> $aggregationChoices
     */
    public function setAggregationChoices(array $aggregationChoices): void
    {
        $this->aggregationChoices = $aggregationChoices;
    }
}
