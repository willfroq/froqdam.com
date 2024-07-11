<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final class ProcessSwitchUploadRequest
{
    /** @param array<int, ValidationError> $errors
     * @throws \Exception
     */
    public function __invoke(Request $request, ?UploadedFile $file, ?string &$customAssetFolder, array &$errors): void
    {
        if (!($file instanceof UploadedFile)) {
            $errors[] = new ValidationError(propertyPath: 'fileContents', message: sprintf('FileContents %s is not a file.', $file));
        }
    }
}
