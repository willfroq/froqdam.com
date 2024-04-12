<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Webhook\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bb-test', name: 'froq_portal_webhook.bb_test', methods: [Request::METHOD_POST])]
final class BettyBlocksController extends AbstractController
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->json(data: ['youSentThis' => json_decode($request->getContent())]);
    }
}
