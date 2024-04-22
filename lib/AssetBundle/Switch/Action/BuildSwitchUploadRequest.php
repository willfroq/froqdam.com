<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildSwitchUploadRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): SwitchUploadRequest
    {
        $errors = [];

        /** @var UploadedFile|null $file */
        $file = $request->files->get('fileContents');

        if (!($file instanceof UploadedFile)) {
            $errors[] = new ValidationError(propertyPath: 'fileContents', message: sprintf('FileContents %s is not a file.', $file));
        }

        $assetFolder = $request->request->get('customAssetFolder');

        $customAssetFolder = $assetFolder === null || $assetFolder === '' ? AssetResourceOrganizationFolderNames::Assets->name : $assetFolder;

        $switchUploadRequest = new SwitchUploadRequest(
            eventName: (string) $request->request->get('eventName'),
            filename: (string) $request->request->get('filename'),
            customerCode: (string) $request->request->get('customerCode'),
            customAssetFolder: (string) $customAssetFolder,
            assetType: (string) $request->request->get('assetType'),
            fileContents: $file,
            assetResourceValidFrom: (string) $request->request->get('assetResourceValidFrom'),
            assetResourceValidUntil: (string) $request->request->get('assetResourceValidUntil'),
            assetResourceMetadataFieldCollection: (string) $request->request->get('assetResourceMetadataFieldCollection'),
            productData: (string) $request->request->get('productData'),
            tagData: (string) $request->request->get('tagData'),
            projectData: (string) $request->request->get('projectData'),
            printerData: (string) $request->request->get('printerData'),
            supplierData: (string) $request->request->get('supplierData'),
            errors: []
        );

        $violations = (array) json_decode($this->serializer->serialize($this->validator->validate($switchUploadRequest), 'json'))?->violations;

        foreach ($violations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $switchUploadRequest->errors = $errors;

        return $switchUploadRequest;
    }
}
