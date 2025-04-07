<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller;

use Froq\AssetBundle\Pimtoday\Action\Upload\Builder\BuildFileRequest;
use Froq\AssetBundle\Pimtoday\Action\Upload\Builder\BuildFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/files', name: 'froq_portal_pimtoday.pimtoday_files', methods: [Request::METHOD_GET])]
final class FileController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(
        Request $request,
        BuildFileRequest $buildFileRequest,
        BuildFileResponse $buildFileResponse,
    ): JsonResponse {
        $validatedRequest = ($buildFileRequest)($request);

        if (count((array) $validatedRequest->errors) > 0) {
            return $this->json(data: ['validationErrors' => $validatedRequest->errors], status: 422);
        }

        return $this->json(data: ($buildFileResponse)($validatedRequest)->toArray());
    }
}
