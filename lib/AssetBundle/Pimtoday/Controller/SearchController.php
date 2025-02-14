<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/search', name: 'froq_portal_pimtoday.pimtoday_search', methods: [Request::METHOD_GET])]
final class SearchController extends AbstractController
{
    public function __invoke(
        Request $request,
    ): JsonResponse {
        return $this->json(data: [
            'testResponse' => 'SUCCESS!',
        ]);
    }
}
