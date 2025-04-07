<?php

namespace Froq\PortalBundle\ESPropertyMapping;

use Pimcore\Log\ApplicationLogger;
use Symfony\Contracts\Service\Attribute\Required;

class AbstractMapper
{
    protected ApplicationLogger $logger;

    #[Required]
    public function setApplicationLogger(ApplicationLogger $logger): void
    {
        $this->logger = $logger;
    }
}
