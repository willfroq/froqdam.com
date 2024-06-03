<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Action\Email\SendCriticalErrorEmail;
use Pimcore\Log\ApplicationLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final class CreateTemporaryFile
{
    public function __construct(
        private readonly string $projectDirectory,
        private readonly ApplicationLogger $applicationLogger,
        private readonly Filesystem $filesystem,
        private readonly SendCriticalErrorEmail $sendCriticalErrorEmail
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): string
    {
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('fileContents');

        $destination = null;
        $newFilename = null;

        if ($uploadedFile instanceof UploadedFile) {
            $destination = "$this->projectDirectory/public/var/tmp/uploads";
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'.'.$uploadedFile->guessExtension();
        }

        try {
            if (!$this->filesystem->exists((string) $destination)) {
                $this->filesystem->mkdir((string) $destination, 0700);
            }

            if (!empty($destination) && !empty($newFilename)) {
                $uploadedFile->move($destination, $newFilename);
            }
        } catch (FileException $exception) {
            $this->applicationLogger->error(message: $exception->getMessage(), context: ['component' => 'upload']);

            ($this->sendCriticalErrorEmail)($newFilename);

            throw new FileException(message: $exception->getMessage());
        }

        return "$destination/$newFilename";
    }
}
