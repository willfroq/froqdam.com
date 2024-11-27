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

        $tagData = (array) json_decode((string) $request->request->get('tagData'), true);

        if (!empty($tagData)) {
            foreach ($tagData as $tagDatum) {
                if (empty($tagDatum['code'])) {
                    $errors[] = new ValidationError(propertyPath: 'tagCode', message: 'tagCode in %s can not be blank.');
                }
            }
        }

        $projectData = (array) json_decode((string) $request->request->get('projectData'), true);

        if (!empty($projectData)) {
            if (empty($projectData['projectName'])) {
                $errors[] = new ValidationError(propertyPath: 'projectName', message: 'projectName in %s can not be blank.');
            }
        }

        if (!empty($projectData)) {
            if (empty($projectData['froqName'])) {
                $errors[] = new ValidationError(propertyPath: 'froqName', message: 'projectName in %s can not be blank.');
            }
        }
    }
}
