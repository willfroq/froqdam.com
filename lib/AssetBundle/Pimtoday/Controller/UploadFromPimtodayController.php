<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller;

use Froq\AssetBundle\Pimtoday\Action\Builder\BuildPimtodayUploadRequest;
use Froq\AssetBundle\Pimtoday\Action\Builder\BuildPimtodayUploadResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/upload', name: 'froq_portal_pimtoday.pimtoday_upload', methods: [Request::METHOD_POST])]
final class UploadFromPimtodayController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(
        Request $request,
        BuildPimtodayUploadRequest $buildPimtodayUploadRequest,
        BuildPimtodayUploadResponse $buildPimtodayUploadResponse,
    ): JsonResponse {
        $validatedRequest = ($buildPimtodayUploadRequest)($request);

        if (count((array) $validatedRequest->errors) > 0) {
            return $this->json(data: ['validationErrors' => $validatedRequest->errors, 'status' => 422], status:  422);
        }

        return $this->json(data: [
            'testResponse' => 'SUCCESS!',
            ...($buildPimtodayUploadResponse)($validatedRequest)->toArray()
        ]);
    }
}