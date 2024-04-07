<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Model\DataObject;

use Pimcore\Model\Asset\MetaData\EmbeddedMetaDataTrait;

class AssetDocument extends \Pimcore\Model\Asset\Document
{
    use EmbeddedMetaDataTrait;
}
