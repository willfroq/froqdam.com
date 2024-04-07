<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Converter;

use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use RtfHtmlPhp\Document;
use RtfHtmlPhp\Html\HtmlFormatter;

class RtfToHtmlConverter
{
    public function __construct(protected readonly ApplicationLogger $logger)
    {
    }

    public function convert(Asset\Text $asset): ?string
    {
        if ($asset->getMimeType() !== 'text/rtf') {
            throw new \InvalidArgumentException('Expected text/rtf Mime type');
        }

        try {
            $document = new Document($asset->getData());
            $formatter = new HtmlFormatter();

            return $formatter->Format($document);
        } catch (\Exception $ex) {
            $this->logger->warning($ex->getMessage());
        }

        return null;
    }
}
