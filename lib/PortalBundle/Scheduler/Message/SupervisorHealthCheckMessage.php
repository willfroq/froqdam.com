<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Scheduler\Message;

use Webmozart\Assert\Assert;

final class SupervisorHealthCheckMessage
{
    public function __construct(
        public string $name,
    ) {
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
