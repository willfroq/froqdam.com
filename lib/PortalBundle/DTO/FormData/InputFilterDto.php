<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO\FormData;

class InputFilterDto
{
    private ?string $text = null;
    private ?string $field = null;

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(?string $field): void
    {
        $this->field = $field;
    }
}
