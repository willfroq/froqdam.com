<?php

declare(strict_types=1);

namespace Froq\AssetBundle\EventSubscriber;

use Froq\AssetBundle\Model\DataObject\AssetDocument;
use Froq\AssetBundle\Utility\FileValidator;
use Pimcore\Event\AdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class PimcoreAdminSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AdminEvents::ASSET_GET_PRE_SEND_DATA => 'onAssetGetPreSendData'
        ];
    }

    public function onAssetGetPreSendData(GenericEvent $e): void
    {
        $data = $e->getArgument('data');
        $asset = $e->getArgument('asset');

        if ($asset instanceof AssetDocument && FileValidator::isValidPdf($asset)) {
            if (!$asset->getEmbeddedMetaData(false)) {
                $asset->getEmbeddedMetaData(true, false); // read Exif, IPTC and XPM like in the old days ...
            }

            $data['customSettings'] = $asset->getCustomSettings();
            $e->setArgument('data', $data);
        }
    }
}
