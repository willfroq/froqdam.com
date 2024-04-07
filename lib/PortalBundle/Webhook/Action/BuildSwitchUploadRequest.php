<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Webhook\Action;

use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Froq\PortalBundle\Webhook\Controller\Request\SwitchUploadRequest;
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

    public function __invoke(Request $request): SwitchUploadRequest
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('fileContents');

        $switchUploadRequest = new SwitchUploadRequest(
            eventName: (string) $request->request->get('eventName'),
            filename: (string) $request->request->get('filename'),
            customerCode: (string) $request->request->get('customerCode'),
            customAssetFolder: (string) $request->request->get('customAssetFolder'),
            assetType: (string) $request->request->get('assetType'),
            fileContents: $file,
            importTagsMetadata: (string) $request->request->get('importTagsMetadata'),
            metadataFrom: (string) $request->request->get('metadataFrom'),
            filenameSeparator: (string) $request->request->get('filenameSeparator'),
            metadataMapping: (string) $request->request->get('metadataMapping'),
            tagsMapping: (string) $request->request->get('tagsMapping'),
            codeReference: (string) $request->request->get('codeReference'),
            errors: []
        );

        $violations = (array) json_decode($this->serializer->serialize($this->validator->validate($switchUploadRequest), 'json'))->violations;

        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        $switchUploadRequest->errors = $errors;

        return $switchUploadRequest;
    }
}
