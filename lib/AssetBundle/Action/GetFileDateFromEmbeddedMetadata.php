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

        $metadataCreateDate = isset($assetDocument->getEmbeddedMetaData(force: true)['CreateDate']) ? $assetDocument->getEmbeddedMetaData(force: true)['CreateDate'] : '';
        $metadataModifyDate = isset($assetDocument->getEmbeddedMetaData(force: true)['ModifyDate']) ? $assetDocument->getEmbeddedMetaData(force: true)['ModifyDate'] : '';
        $exifCreateDate = isset($assetDocument->getEXIFData()['FileDateTime']) ? $assetDocument->getEXIFData()['FileDateTime'] : '';
        $exifModifyDate = isset($assetDocument->getEXIFData()['FileDateTime']) ? $assetDocument->getEXIFData()['FileDateTime'] : '';
        $xmpCreateDate = isset($assetDocument->getXMPData()['CreateDate']) ? $assetDocument->getXMPData()['CreateDate'] : '';
        $xmpModifyDate = isset($assetDocument->getXMPData()['ModifyDate']) ? $assetDocument->getXMPData()['ModifyDate'] : '';
        $iptcCreateDate = isset($assetDocument->getIPTCData()['CreateDate']) ? $assetDocument->getIPTCData()['CreateDate'] : '';
        $iptcModifyDate = isset($assetDocument->getIPTCData()['ModifyDate']) ? $assetDocument->getIPTCData()['ModifyDate'] : '';

        return match (true) {
            !empty($metadataCreateDate) && !empty($metadataModifyDate)  => new FileDate(createDate: $metadataCreateDate, modifyDate: $metadataModifyDate),
            !empty($exifCreateDate) && !empty($exifModifyDate)  => new FileDate(createDate: $exifCreateDate, modifyDate: $exifModifyDate),
            !empty($xmpCreateDate) && !empty($xmpModifyDate)  => new FileDate(createDate: $xmpCreateDate, modifyDate: $xmpModifyDate),
            !empty($iptcCreateDate) && !empty($iptcModifyDate)  => new FileDate(createDate: $iptcCreateDate, modifyDate: $iptcModifyDate),

            default => null
        };
    }
}
