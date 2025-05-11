<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Action\Update\BuildUpdateRequest;
use Froq\AssetBundle\Switch\Action\Update\BuildUpdateResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/update', name: 'froq_portal_switch.switch_update', methods: [Request::METHOD_POST])]
final class UpdateController extends AbstractController
{
    /**
     * @throws \Exception
     * @throws Exception
     */
    public function __invoke(
        Request $request,
        BuildUpdateRequest $buildUpdateRequest,
        BuildUpdateResponse $buildUpdateResponse
    ): JsonResponse {
        $validatedRequest = ($buildUpdateRequest)($request);

        if (count((array) $validatedRequest->errors) > 0) {
            return $this->json(data: ['validationErrors' => $validatedRequest->errors, 'status' => 422], status:  422);
        }

        return $this->json(data: ($buildUpdateResponse)($validatedRequest)->toArray());
    }
}
