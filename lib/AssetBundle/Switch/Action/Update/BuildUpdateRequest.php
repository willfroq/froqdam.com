<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Update;

use Froq\AssetBundle\Switch\Controller\Request\UpdateRequest;
use Froq\AssetBundle\Switch\ValueObject\ValidationError;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildUpdateRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): UpdateRequest
    {
        $customAssetFolder = empty($request->request->get('customAssetFolder')) ? 'Assets' : (string) $request->request->get('customAssetFolder');

        $errors = [];

        $customerCode = (string) $request->request->get('customerCode');

        $organization = Organization::getByCode($customerCode)?->current(); /** @phpstan-ignore-line */
        $filename = (string) $request->request->get('filename');

        if (!($organization instanceof Organization)) {
            $errors[] = new ValidationError(propertyPath: 'organization', message: 'organization in %s does not exist.');
        }

        $validOrganization = $organization instanceof Organization ? $organization : null;

        $parentAssetResource = (new AssetResource\Listing())
            ->addConditionParam('o_key = ?', $filename)
            ->addConditionParam('o_path = ?', $validOrganization?->getObjectFolder()."/$customAssetFolder/")
            ->current();

        if (!($parentAssetResource instanceof AssetResource)) {
            $errors[] = new ValidationError(propertyPath: 'filename', message: 'filename in %s does not exist.');
        }

        $tagData = (array) json_decode((string) $request->request->get('tagData'), true);

        if (!empty($tagData)) {
            foreach ($tagData as $tagDatum) {
                if (empty($tagDatum['code'])) {
                    $errors[] = new ValidationError(propertyPath: 'tagCode', message: 'tagCode in %s can not be blank.');
                }
            }
        }

        $projectData = (array) json_decode((string) $request->request->get('projectData'), true);

        if (!isset($projectData['projectName'])) {
            $errors[] = new ValidationError(propertyPath: 'projectName', message: 'projectName in %s can not be blank.');
        }

        if (!isset($projectData['froqName'])) {
            $errors[] = new ValidationError(propertyPath: 'froqName', message: 'froqName in %s can not be blank.');
        }

        $validParentAssetResource = $parentAssetResource instanceof AssetResource ? $parentAssetResource : null;

        if (!$validParentAssetResource?->getNeedsReprocess()) {
            $errors[] = new ValidationError(propertyPath: 'AssetResource', message: 'AssetResource does not need reprocessing.');
        }

        $updateRequest = new UpdateRequest(
            eventName: (string) $request->request->get('eventName'),
            filename: $filename,
            parentAssetResourceId: (int) $validParentAssetResource?->getId(),
            organizationId: (int) $validOrganization?->getId(),
            parentAssetResourceFolderPath: $validOrganization?->getObjectFolder().'/',
            customerCode: $customerCode,
            customAssetFolder: $customAssetFolder,
            assetType: (string) $request->request->get('assetType'),
            assetResourceMetadataFieldCollection: (string) $request->request->get('assetResourceMetadataFieldCollection'),
            productData: (string) $request->request->get('productData'),
            tagData: (string) $request->request->get('tagData'),
            projectData: (string) $request->request->get('projectData'),
            printerData: (string) $request->request->get('printerData'),
            supplierData: (string) $request->request->get('supplierData'),
            errors: []
        );

        $violations = (array) json_decode($this->serializer->serialize($this->validator->validate($updateRequest), 'json'))?->violations;

        foreach ($violations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $updateRequest->errors = $errors;

        return $updateRequest;
    }
}
