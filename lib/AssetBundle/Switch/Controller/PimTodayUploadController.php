<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/upload', name: 'froq_portal_pimtoday.upload', methods: [Request::METHOD_POST])]
final class PimTodayUploadController extends AbstractController
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->json(data: [
            'payload' => $request->request->get('payload'),
        ]);
    }
}
