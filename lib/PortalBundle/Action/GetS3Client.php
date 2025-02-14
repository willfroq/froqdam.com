<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action;

use Aws\S3\S3Client;

final class GetS3Client
{
    public function __invoke(): S3Client
    {
        return new S3Client([
            'version' => 'latest',
            'region' => $_ENV['AWS_S3_BUCKET_REGION'] ?? '',
            'credentials' => [
                'key' => $_ENV['YOUWE_S3_STORAGE_KEY'] ?? '',
                'secret' => $_ENV['YOUWE_S3_STORAGE_SECRET'] ?? '',
            ],
        ]);

    }
}
