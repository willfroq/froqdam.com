<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Upload\Builder;

use Froq\AssetBundle\Pimtoday\Controller\Request\FileRequest;
use Froq\AssetBundle\Pimtoday\ValueObject\ValidationError;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildFileRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): FileRequest
    {
        $errors = [];

        $fileRequest = new FileRequest(
            assetResourceId: (int) $request->get(key: 'assetResourceId'),
            errors: $errors
        );

        $assetResource = AssetResource::getById((int) $fileRequest->assetResourceId);

        if (!($assetResource instanceof AssetResource)) {
            $errors[] = new ValidationError(propertyPath: 'assetResource', message: 'File does not exist');
        }

        $asset = $assetResource?->getAsset();

        if (!($asset instanceof Asset)) {
            $errors[] = new ValidationError(propertyPath: 'asset', message: 'File does not exist');
        }

        $parentAssetResource = $assetResource?->getParent();

        if (!($parentAssetResource instanceof AssetResource)) {
            $errors[] = new ValidationError(propertyPath: 'parentAssetResource', message: 'ParentAssetResource does not exist');
        }

        $parentAssetResourcePayload = $parentAssetResource instanceof AssetResource ? $parentAssetResource : null;

        $organizationPayload = current((array) $parentAssetResourcePayload?->getOrganizations()) !== false ? current((array) $parentAssetResourcePayload?->getOrganizations()) : null;

        $organization = $organizationPayload instanceof Organization ? $organizationPayload : null;

        if (!in_array(
            needle: $organization?->getId(),
            haystack: array_filter(
                array_map(
                    fn (AbstractObject $organization) => $organization->getId(),
                    $parentAssetResource instanceof AssetResource ? (array) $parentAssetResource->getOrganizations() : []
                )
            ))
        ) {
            $errors[] = new ValidationError(propertyPath: 'assetResource', message: 'Invalid Request!');
        }

        $fileRequestViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($fileRequest), 'json'))?->violations;

        foreach ($fileRequestViolations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $fileRequest->errors = $errors;

        return $fileRequest;
    }
}
