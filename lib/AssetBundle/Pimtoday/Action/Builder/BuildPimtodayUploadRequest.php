<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Builder;

use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadRequest;
use Froq\AssetBundle\Pimtoday\ValueObject\DocumentFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ProjectFromPayload;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildPimtodayUploadRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): PimtodayUploadRequest
    {
        $payload = (array) json_decode($request->getContent());

        $errors = [];

        if (empty($payload)) {
            $errors[] = new ValidationError(propertyPath: 'payloadData', message: 'Payload can not be empty');
        }

        $projectFromPayload = new ProjectFromPayload(
            projectNumber: $payload['projectNumber'] ?? '',
            projectName: $payload['projectName'] ?? '',
            description: $payload['description'] ?? '',
            projectType: $payload['projectType'] ?? '',
            status: $payload['status'] ?? '',
            location: $payload['location'] ?? '',
            projectOwner: $payload['projectOwner'] ?? '',
        );

        $documentFromPayload = new DocumentFromPayload(
            documentIdentifier: $payload['documentIdentifier'] ?? 0,
            documentName: $payload['documentName'] ?? '',
            documentType: $payload['documentType'] ?? '',
            documentFileType: $payload['documentFileType'] ?? '',
            documentExtension: $payload['documentExtension'] ?? '',
            documentStatus: $payload['documentStatus'] ?? '',
            documentSku: $payload['documentSku'] ?? '',
            documentSkuId: $payload['documentSkuId'] ?? 0,
            documentIsProcessed: $payload['documentIsProcessed'] ?? false,
            documentIsSentToSftp: $payload['documentName'] ?? false,
            documentIsReplaced: $payload['documentName'] ?? false,
        );

        $pimtodayUploadRequest = new PimtodayUploadRequest(
            eventName: $payload['eventName'] ?? '',
            projectData: $projectFromPayload,
            documentData: $documentFromPayload,
            file: $payload['file'] ?? '',
            errors: []
        );

        $projectFromPayloadViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($projectFromPayload), 'json'))?->violations;

        foreach ($projectFromPayloadViolations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $documentFromPayloadViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($documentFromPayload), 'json'))?->violations;

        foreach ($documentFromPayloadViolations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $pimtodayUploadRequestViolations = (array) json_decode($this->serializer->serialize($this->validator->validate($pimtodayUploadRequest), 'json'))?->violations;

        foreach ($pimtodayUploadRequestViolations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $pimtodayUploadRequest->errors = $errors;

        return $pimtodayUploadRequest;
    }
}
