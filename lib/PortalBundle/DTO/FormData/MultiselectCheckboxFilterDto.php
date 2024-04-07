<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO\FormData;

class MultiselectCheckboxFilterDto
{
    /**
     * @var array<int, bool|float|int|string>
     */
    private array $selectedOptions = [];

    /**
     * @return array<int, bool|float|int|string>
     */
    public function getSelectedOptions(): array
    {
        return $this->selectedOptions;
    }

    /**
     * @param array<int, bool|float|int|string> $selectedOptions
     */
    public function setSelectedOptions(array $selectedOptions): void
    {
        $this->selectedOptions = $selectedOptions;
    }
}
