<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Upload\Builder;

use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadRequest;
use Froq\AssetBundle\Pimtoday\ValueObject\DocumentFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ProductFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ProjectFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ValidationError;
use Pimcore\Model\DataObject\Organization;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildPimtodayUploadRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): PimtodayUploadRequest
    {
        $errors = [];

        $document = $request->request->get('documentData');
        $project = $request->request->get('projectData');
        $product = $request->request->get('productData');

        $documentFromPayload= null;

        if (!empty($document) && isset($document['pimTodayId'])) {
            $documentFromPayload = new DocumentFromPayload(
                pimTodayId: (int) $document['pimTodayId'],
                damId: (int) $document['damId'],
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
            $projectFromPayload = new ProjectFromPayload(
                pimTodayId: (int) $project['pimTodayId'],
                damId: (int) $project['damId'],
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
            $productFromPayload = new ProductFromPayload(
                pimTodayId: (int) $product['pimTodayId'],
                damId: (int) $product['damId'],
                pimTodaySku: $product['pimTodaySku'] ?? '',
                pimTodayEan: $product['pimTodayEan'] ?? '',
            );

            $productFromPayloadViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($productFromPayload), 'json'))?->violations;

            foreach ($productFromPayloadViolations as $violation) {
                $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
            }
        }

        $organization = Organization::getById((int) $request->request->get('damOrganizationId'));

        if (!($organization instanceof Organization)) {
            $errors[] = new ValidationError(propertyPath: 'organization', message: 'Dam organization does not exist');
        }

        $uploadedFile = $request->files->get('fileContents');

        if (!($uploadedFile instanceof UploadedFile)) {
            $errors[] = new ValidationError(propertyPath: 'file', message: 'No file.');
        }

        $pimtodayUploadRequest = new PimtodayUploadRequest(
            eventName: (string) $request->request->get('eventName'),
            damOrganizationId: (int) $request->request->get('damOrganizationId'),
            projectData: $projectFromPayload,
            documentData: $documentFromPayload,
            productData: $productFromPayload,
            fileBase64: (string) $request->request->get('fileBase64'),
            fileContents: $uploadedFile,
            organization: $organization,
            errors: []
        );

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
