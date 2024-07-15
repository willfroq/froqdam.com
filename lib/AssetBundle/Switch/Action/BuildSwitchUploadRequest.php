<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildSwitchUploadRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        private readonly ProcessSwitchUploadRequest $processSwitchUploadRequest,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): SwitchUploadRequest
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('fileContents');
        $customAssetFolder = (string) $request->request->get('customAssetFolder');

        $errors = [];

        ($this->processSwitchUploadRequest)($request, $file, $customAssetFolder, $errors);

        $switchUploadRequest = new SwitchUploadRequest(
            eventName: (string) $request->request->get('eventName'),
            filename: (string) $request->request->get('filename'),
            customerCode: (string) $request->request->get('customerCode'),
            customAssetFolder: $customAssetFolder,
            assetType: (string) $request->request->get('assetType'),
            fileContents: $file,
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
