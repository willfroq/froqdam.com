<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO\FormData;

class InputFilterDto
{
    private ?string $text =null;

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
}
