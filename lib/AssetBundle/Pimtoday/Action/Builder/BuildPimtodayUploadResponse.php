<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Builder;

use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadRequest;
use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadResponse;

final class BuildPimtodayUploadResponse
{
    public function __invoke(PimtodayUploadRequest $pimtodayUploadRequest): PimtodayUploadResponse
    {
        // TODO Create or Update Asset
        // TODO Create or Update AssetResource
        // TODO Create or Update Project

        return new PimtodayUploadResponse(
            eventName: $pimtodayUploadRequest->eventName,
            date: date('F j, Y H:i'),
            assetId: '',
            assetResourceId: '',
            uploadedDamProjectId: '',
        );
    }
}
