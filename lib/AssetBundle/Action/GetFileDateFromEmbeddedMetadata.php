<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Froq\AssetBundle\ValueObject\FileDate;
use Pimcore\Model\Asset;

final class GetFileDateFromEmbeddedMetadata
{
    public function __invoke(Asset $asset): ?FileDate
    {
        $metadataCreateDate = isset($asset->getEmbeddedMetaData(force: true)['CreateDate']) ? $asset->getEmbeddedMetaData(force: true)['CreateDate'] : ''; /** @phpstan-ignore-line */
        $metadataModifyDate = isset($asset->getEmbeddedMetaData(force: true)['ModifyDate']) ? $asset->getEmbeddedMetaData(force: true)['ModifyDate'] : ''; /** @phpstan-ignore-line */
        $exifCreateDate = isset($asset->getEXIFData()['FileDateTime']) ? $asset->getEXIFData()['FileDateTime'] : ''; /** @phpstan-ignore-line */
        $exifModifyDate = isset($asset->getEXIFData()['FileDateTime']) ? $asset->getEXIFData()['FileDateTime'] : ''; /** @phpstan-ignore-line */
        $xmpCreateDate = isset($asset->getXMPData()['CreateDate']) ? $asset->getXMPData()['CreateDate'] : ''; /** @phpstan-ignore-line */
        $xmpModifyDate = isset($asset->getXMPData()['ModifyDate']) ? $asset->getXMPData()['ModifyDate'] : ''; /** @phpstan-ignore-line */
        $iptcCreateDate = isset($asset->getIPTCData()['CreateDate']) ? $asset->getIPTCData()['CreateDate'] : ''; /** @phpstan-ignore-line */
        $iptcModifyDate = isset($asset->getIPTCData()['ModifyDate']) ? $asset->getIPTCData()['ModifyDate'] : ''; /** @phpstan-ignore-line */

        return match (true) {
            !empty($metadataCreateDate) && !empty($metadataModifyDate)  => new FileDate(createDate: $metadataCreateDate, modifyDate: $metadataModifyDate),
            !empty($exifCreateDate) && !empty($exifModifyDate)  => new FileDate(createDate: $exifCreateDate, modifyDate: $exifModifyDate),
            !empty($xmpCreateDate) && !empty($xmpModifyDate)  => new FileDate(createDate: $xmpCreateDate, modifyDate: $xmpModifyDate),
            !empty($iptcCreateDate) && !empty($iptcModifyDate)  => new FileDate(createDate: $iptcCreateDate, modifyDate: $iptcModifyDate),

            default => null
        };
    }
}
