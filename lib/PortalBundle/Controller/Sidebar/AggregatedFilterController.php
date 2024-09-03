<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\Sidebar;

use MembersBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AggregatedFilterController extends AbstractController
{
    #[Route('/aggregated-filter', name: 'froq_filter.aggregated_filter', methods: Request::METHOD_GET)]
    public function __invoke(Request $request): Response
    {
        return $this->json(['hi' => $request->get(key: 'id')]);
    }
}
