<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO\FormData;

class NumberRangeFilterDto
{
    private ?float $min = null;
    private ?float $max = null;

    /**
     * @return float|null
     */
    public function getMin(): ?float
    {
        return $this->min;
    }

    /**
     * @param float|null $min
     */
    public function setMin(?float $min): void
    {
        $this->min = $min;
    }

    /**
     * @return float|null
     */
    public function getMax(): ?float
    {
        return $this->max;
    }

    /**
     * @param float|null $max
     */
    public function setMax(?float $max): void
    {
        $this->max = $max;
    }
}
