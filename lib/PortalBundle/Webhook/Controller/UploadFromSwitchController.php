<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Webhook\Controller;

use Froq\PortalBundle\Webhook\Action\BuildSwitchUploadRequest;
use Froq\PortalBundle\Webhook\Action\BuildSwitchUploadResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/switch-upload', name: 'froq_portal_webhook.switch_upload', methods: [Request::METHOD_POST])]
final class UploadFromSwitchController extends AbstractController
{
    /**
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
