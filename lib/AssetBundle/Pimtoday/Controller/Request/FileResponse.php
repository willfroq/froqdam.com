<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller\Request;

final class FileResponse
{
    public function __construct(
        public ?string $date,

        public ?int $pimtodayProjectId,

        public ?int $damProjectId,

        public ?int $pimtodaySkuId,

        public ?int $damSkuId,

        public ?int $pimtodayDocumentId,

        public ?int $damAssetResourceId,

        public string $gridThumbnailLink,

        public string $listThumbnailLink,

        public string $imagePreviewLink,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'pimtodayProjectId' => $this->pimtodayProjectId,
            'damProjectId' => $this->damProjectId,
            'pimtodaySkuId' => $this->pimtodaySkuId,
            'damSkuId' => $this->damSkuId,
            'damAssetResourceId' => $this->damAssetResourceId,
            'gridThumbnailLink' => $this->gridThumbnailLink,
            'listThumbnailLink' => $this->listThumbnailLink,
            'imagePreviewLink' => $this->imagePreviewLink,
        ];
    }
}
