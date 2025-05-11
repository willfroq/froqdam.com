<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch;

use Elastica\Document;
use JoliCode\Elastically\Messenger\DocumentExchangerInterface;

final class DocumentExchanger implements DocumentExchangerInterface
{
    public function fetchDocument(string $className, string $id): ?Document
    {
        return new Document($id, ['id' => 1]);
    }
}
