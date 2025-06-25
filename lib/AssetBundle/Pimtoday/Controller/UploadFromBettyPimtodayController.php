<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller;

use Froq\AssetBundle\Pimtoday\Action\Upload\Builder\BuildPimtodayBBUploadRequest;
use Froq\AssetBundle\Pimtoday\Action\Upload\Builder\BuildPimtodayUploadResponse;
use Pimcore\Log\ApplicationLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/upload/betty', name: 'froq_portal_pimtoday.pimtoday_upload_betty', methods: [Request::METHOD_POST])]
final class UploadFromBettyPimtodayController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(
        Request $request,
        BuildPimtodayBBUploadRequest $buildPimtodayBBUploadRequest,
        BuildPimtodayUploadResponse $buildPimtodayUploadResponse,
        ApplicationLogger $logger,
    ): JsonResponse {
        $validatedRequest = ($buildPimtodayBBUploadRequest)($request);

        if (count((array) $validatedRequest->errors) > 0) {
            return $this->json(data: ['validationErrors' => $validatedRequest->errors], status: 422);
        }

        return $this->json(data: ($buildPimtodayUploadResponse)($validatedRequest)->toArray());
    }
}
