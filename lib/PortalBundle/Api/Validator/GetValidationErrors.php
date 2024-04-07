<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Validator;

use Froq\PortalBundle\Api\Request\AssetLibraryRequest;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetValidationErrors
{
    public function __construct(private readonly ValidatorInterface $validator, private readonly SerializerInterface $serializer)
    {
    }

    /**
     * @return array<int, ValidationError>
     */
    public function __invoke(AssetLibraryRequest $assetLibraryRequest): array
    {
        $violations = (array) json_decode($this->serializer->serialize($this->validator->validate($assetLibraryRequest), 'json'))->violations;

        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = new ValidationError(propertyPath: (string) $violation->propertyPath, message: (string) $violation->title);
        }

        return $errors;
    }
}
