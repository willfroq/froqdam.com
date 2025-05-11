<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class UserGroupExportCollection
{
    public function __construct(
        public readonly int $offset,
        public readonly int $limit,
        public readonly int $totalCount,
        /** @var array<int, UserGroupExportItem> */
        public readonly array $userGroupExportItems,
        public readonly ?ClientExportCollection $clients,
    ) {
        Assert::numeric($this->offset, 'Expected "totalPages" to be a numeric, got %s');
        Assert::numeric($this->limit, 'Expected "currentPage" to be a numeric, got %s');
        Assert::numeric($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::isArray($this->userGroupExportItems, 'Expected "userGroupExportItems" to be an array, got %s');
        Assert::nullOrIsInstanceOf($this->clients, ClientExportCollection::class, 'Expected "clients" to be instance of, got %s');
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
            'totalCount' => $this->totalCount,
            'userGroupExportItems' => $this->userGroupExportItems,
            'clients' => $this->clients,
        ];
    }
}
