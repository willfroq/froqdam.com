<?php

namespace Froq\PortalBundle\Api\Action;

use Symfony\Component\HttpFoundation\Request;

final class GetBaseUrl
{
    public function __invoke(): string
    {
        return Request::createFromGlobals()->getSchemeAndHttpHost();
    }
}
