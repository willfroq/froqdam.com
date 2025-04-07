<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action\Request;

use Froq\AssetBundle\ValueObject\ValidationError;
use Froq\PortalBundle\DataTransferObject\Request\DownloadLinksRequest;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildDownloadLinksRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, User $user): DownloadLinksRequest
    {
        $errors = [];

        $downloadLinksRequest = new DownloadLinksRequest(
            assetResourceIds: (array) json_decode((string) $request->getContent(), true),
            user: $user,
            errors: []
        );

        foreach ($downloadLinksRequest->assetResourceIds as $assetResourceId) {
            if (!is_numeric($assetResourceId)) {
                continue;
            }

            $assetResource = AssetResource::getById((int) $assetResourceId);

            if (!($assetResource instanceof AssetResource)) {
                $errors[] = new ValidationError(propertyPath: 'AssetResource', message: sprintf('%s does not exist.', $assetResourceId));
            }

            $asset = $assetResource?->getAsset();

            if (!($asset instanceof Asset)) {
                $errors[] = new ValidationError(propertyPath: 'AssetResource', message: sprintf('%s does not have a file.', $assetResourceId));
            }
        }

        $violations = (array) json_decode($this->serializer->serialize($this->validator->validate($downloadLinksRequest), 'json'))?->violations;

        foreach ($violations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $downloadLinksRequest->errors = $errors;

        return $downloadLinksRequest;
    }
}
