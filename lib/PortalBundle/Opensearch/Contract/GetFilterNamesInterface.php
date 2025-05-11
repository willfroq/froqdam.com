<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Contract;

interface GetFilterNamesInterface
{
    /**
     * @return array<int, string>
     */
    public function __invoke(): array;
}
