<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Action\BuildSwitchUploadRequest;
use Froq\AssetBundle\Switch\Action\BuildSwitchUploadResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/upload', name: 'froq_portal_switch.upload', methods: [Request::METHOD_POST])]
final class UploadFromSwitchController extends AbstractController
{
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function __invoke(Request $request, BuildSwitchUploadRequest $buildSwitchUploadRequest, BuildSwitchUploadResponse $buildSwitchUploadResponse): JsonResponse
    {
        $validatedRequest = ($buildSwitchUploadRequest)($request);

        if (count((array) $validatedRequest->errors) > 0) {
            return $this->json(data: ['validationErrors' => $validatedRequest->errors, 'status' => 422], status:  422);
        }

        $response = ($buildSwitchUploadResponse)($validatedRequest);

        return $this->json(data: $response->toArray());
    }
}
