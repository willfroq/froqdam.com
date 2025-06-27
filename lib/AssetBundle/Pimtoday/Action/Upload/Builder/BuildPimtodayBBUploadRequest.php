<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Upload\Builder;

use Froq\AssetBundle\Pimtoday\Action\Upload\Base64ToUploadedFile;
use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadRequest;
use Froq\AssetBundle\Pimtoday\ValueObject\DocumentFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ProductFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ProjectFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ValidationError;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildPimtodayBBUploadRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        private readonly Base64ToUploadedFile $base64ToUploadedFile,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): PimtodayUploadRequest
    {
        $errors = [];

        $payload = json_decode($request->getContent());

        $payloadArray = json_decode((string) json_encode($payload), true);

        $document = $payloadArray['documentData'] ?? [];
        $project = $payloadArray['projectData'] ?? [];
        $product = $payloadArray['productData'] ?? [];

        $documentFromPayload= null;

        if (!empty($document) && isset($document['pimTodayId'])) {
            $documentPimTodayId = $document['pimTodayId'];
            $documentDamId = $document['damId'] ?? null;

            $documentFromPayload = new DocumentFromPayload(
                pimTodayId: (int) $documentPimTodayId,
                damId: (int) $documentDamId,
                documentIdentifier: $document['documentIdentifier'] ?? null,
                damFilename: $document['damFilename'] ?? null,
                damVersion: $document['damVersion'] ?? null,
                documentName: $document['documentName'] ?? null,
                documentType: $document['documentType'] ?? null,
                documentFileType: $document['documentFileType'] ?? null,
                documentExtension: $document['documentExtension'] ?? null,
                documentStatus: $document['documentStatus'] ?? null,
                documentSku: $document['documentSku'] ?? null,
                documentSkuId: $document['documentSkuId'] ?? null,
                documentIsProcessed: $document['documentIsProcessed'] === '1',
                documentIsSentToSftp: $document['documentIsProcessed'] === '1',
                documentIsReplaced: $document['documentIsProcessed'] === '1',
            );

            $documentFromPayloadViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($documentFromPayload), 'json'))?->violations;

            foreach ($documentFromPayloadViolations as $violation) {
                $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
            }
        }

        $fileName = $documentFromPayload?->documentName;

        if (empty($fileName)) {
            $errors[] = new ValidationError(propertyPath: 'fileName', message: 'Must have fileName!');
        }

        $projectFromPayload= null;

        if (!empty($project) && isset($project['pimTodayId'])) {
            $projectPimTodayId = $project['pimTodayId'];
            $projectDamId = $project['damId'] ?? null;

            $projectFromPayload = new ProjectFromPayload(
                pimTodayId: (int) $projectPimTodayId,
                damId: (int) $projectDamId,
                projectNumber: $project['projectNumber'] ?? '',
                froqProjectNumber: $project['froqProjectNumber'] ?? '',
                projectName: $project['projectName'] ?? '',
                description: $project['description'] ?? '',
                projectType: $project['projectType'] ?? '',
                status: $project['status'] ?? '',
                location: $project['location'] ?? '',
                projectOwner: $project['projectOwner'] ?? '',
            );

            $projectFromPayloadViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($projectFromPayload), 'json'))?->violations;

            foreach ($projectFromPayloadViolations as $violation) {
                $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
            }
        }

        $productFromPayload = null;

        if (!empty($product) && isset($product['pimTodayId'])) {
            $productPimTodayId = $product['pimTodayId'];
            $productDamId = $product['damId'] ?? null;

            $productFromPayload = new ProductFromPayload(
                pimTodayId: (int) $productPimTodayId,
                damId: (int) $productDamId,
                pimTodaySku: $product['pimTodaySku'] ?? '',
                pimTodayEan: $product['pimTodayEan'] ?? '',
            );

            $productFromPayloadViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($productFromPayload), 'json'))?->violations;

            foreach ($productFromPayloadViolations as $violation) {
                $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
            }
        }

        $organization = Organization::getById((int) $payloadArray['damOrganizationId']);

        if (!($organization instanceof Organization)) {
            $errors[] = new ValidationError(propertyPath: 'organization', message: 'Dam organization does not exist');
        }

        $fileBase64 = (string) $payloadArray['fileBase64'];

        $uploadedFile = null;

        if (!empty($fileBase64)) {
            $uploadedFile = ($this->base64ToUploadedFile)($fileBase64, (string) $fileName);
        }

        if (!($uploadedFile instanceof UploadedFile)) {
            $uploadedFile = $request->files->get('fileContents');
        }

        if (!($uploadedFile instanceof UploadedFile)) {
            $errors[] = new ValidationError(propertyPath: 'file', message: 'FILE ERROR! Make sure file is not corrupted!.');
        }

        $createOrUpdate = $payloadArray['createOrUpdate'] ?? null;

        if (!in_array(needle: $createOrUpdate, haystack: ['create', 'update'])) {
            $errors[] = new ValidationError(propertyPath: 'createOrUpdate', message: 'createOrUpdate field is required!');
        }

        if ($createOrUpdate === 'update') {
            $existingAssetResource = AssetResource::getById((int) $documentFromPayload?->damId);

            if (!($existingAssetResource instanceof AssetResource)) {
                $errors[] = new ValidationError(propertyPath: 'createOrUpdate', message: 'File to Update does NOT exist!');
            }
        }

        $pimtodayUploadRequest = new PimtodayUploadRequest(
            eventName: (string) $payloadArray['eventName'],
            damOrganizationId: (int) $payloadArray['damOrganizationId'],
            projectData: $projectFromPayload,
            documentData: $documentFromPayload,
            productData: $productFromPayload,
            fileBase64: (string) $payloadArray['fileBase64'],
            fileContents: $uploadedFile,
            organization: $organization,
            createOrUpdate: $payloadArray['createOrUpdate'],
            errors: []
        );

        $product = Product::getByPimTodayId($pimtodayUploadRequest->productData->pimTodayId)?->current(); /** @phpstan-ignore-line */

        if (!($product instanceof Product) && $pimtodayUploadRequest->createOrUpdate === 'update') {
            $errors[] = new ValidationError(propertyPath: 'product', message: 'Can NOT update product that does not exist!');
        }

        $project = Project::getByPimTodayId($pimtodayUploadRequest->projectData->pimTodayId)?->current(); /** @phpstan-ignore-line */

        if (!($project instanceof Project) && $pimtodayUploadRequest->createOrUpdate === 'update') {
            $errors[] = new ValidationError(propertyPath: 'product', message: 'Can NOT update project that does not exist!');
        }

        if (empty($pimtodayUploadRequest->fileContents) && empty($pimtodayUploadRequest->fileBase64)) {
            $errors[] = new ValidationError(propertyPath: 'file', message: sprintf("Can't upload without file: %s", date('F j, Y H:i')));
        }

        $pimtodayUploadRequestViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($pimtodayUploadRequest), 'json'))?->violations;

        foreach ($pimtodayUploadRequestViolations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $pimtodayUploadRequest->errors = $errors;

        foreach ($pimtodayUploadRequest->errors as $item) {
            $this->logger->error($item->message);
        }

        return $pimtodayUploadRequest;
    }
}
