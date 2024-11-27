<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller;

use Froq\AssetBundle\Switch\Action\Cleanup\BuildCleanupAssetsResponse;
use Froq\AssetBundle\Switch\Controller\Request\CleanupAssetsRequest;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/clean-assets', name: 'froq_portal_switch.switch_cleanup_assets', methods: [Request::METHOD_POST])]
final class CleanupAssetsController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        BuildCleanupAssetsResponse $buildCleanupAssetsResponse
    ): JsonResponse {
        $errors = [];

        $cleanupRequest = new CleanupAssetsRequest(
            eventName: (string) $request->get('eventName'),
            customerCode: (string) $request->get('customerCode'),
        );

        $violations = (array) json_decode($serializer->serialize($validator->validate($cleanupRequest), 'json'))?->violations;

        foreach ($violations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        if (count($errors) > 0) {
            return $this->json(data: ['validationErrors' => $errors, 'status' => 422], status:  422);
        }

        return $this->json(data: ($buildCleanupAssetsResponse)($cleanupRequest)->toArray());
    }
}
