<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Froq\AssetBundle\Model\DataObject\AssetDocument;
use Froq\AssetBundle\ValueObject\FileDate;
use Pimcore\Model\Asset;

final class GetFileDateFromEmbeddedMetadata
{
    /**
     * @throws \Exception
     */
    public function __invoke(Asset $asset): ?FileDate
    {
        if (!($asset instanceof AssetDocument)) {
            return null;
        }

        $assetDocument = $asset;

        $metadataCreateDate = $this->checkFileDateFormat($assetDocument->getEmbeddedMetaData(force: true), 'CreateDate');
        $metadataModifyDate = $this->checkFileDateFormat($assetDocument->getEmbeddedMetaData(force: true), 'ModifyDate');
        $exifCreateDate = $this->checkFileDateFormat($assetDocument->getEXIFData(), 'FileDateTime');
        $exifModifyDate = $this->checkFileDateFormat($assetDocument->getEXIFData(), 'FileDateTime');
        $xmpCreateDate = $this->checkFileDateFormat($assetDocument->getXMPData(), 'CreateDate');
        $xmpModifyDate = $this->checkFileDateFormat($assetDocument->getXMPData(), 'ModifyDate');
        $iptcCreateDate = $this->checkFileDateFormat($assetDocument->getIPTCData(), 'CreateDate');
        $iptcModifyDate = $this->checkFileDateFormat($assetDocument->getIPTCData(), 'ModifyDate');

        return match (true) {
            !empty($metadataCreateDate) && !empty($metadataModifyDate)  => new FileDate(createDate: $metadataCreateDate, modifyDate: $metadataModifyDate),
            !empty($exifCreateDate) && !empty($exifModifyDate)  => new FileDate(createDate: $exifCreateDate, modifyDate: $exifModifyDate),
            !empty($xmpCreateDate) && !empty($xmpModifyDate)  => new FileDate(createDate: $xmpCreateDate, modifyDate: $xmpModifyDate),
            !empty($iptcCreateDate) && !empty($iptcModifyDate)  => new FileDate(createDate: $iptcCreateDate, modifyDate: $iptcModifyDate),

            default => null
        };
    }

    /**
     * @throws \Exception
     *
     * @param array<string, mixed> $fileData
     */
    private function checkFileDateFormat(array $fileData, string $dateKey): string
    {
        $dateTime = $fileData[$dateKey] ?? '';

        $allowedPatternOne = '/^\d{4}:\d{2}:\d{2} \d{2}:\d{2}\+\d{2}:\d{2}$/'; // Format: 2012:01:31 14:24+01:00
        $allowedPatternTwo = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}\+\d{2}:\d{2}$/'; // Format: 2012-01-31 14:24+01:00

        if (preg_match($allowedPatternOne, $dateTime) || preg_match($allowedPatternTwo, $dateTime)) {
            return (string) preg_replace(
                '/^(\d{4}):(\d{2}):(\d{2})/',
                '$1-$2-$3',
                $dateTime
            );
        }

        return '';
    }
}
