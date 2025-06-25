<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

final class GetPaginator
{
    /**
     * @return array<string, int>
     */
    public function __invoke(int $requestedPage, int $requestedSize, int $totalCount, int $defaultPageSize = 24): array
    {
        $requestedSize = 0 === $requestedSize ? $defaultPageSize : $requestedSize;
        $totalItems = $totalCount;

        $maxPages = ($totalItems > 0) ? ceil($totalItems / $requestedSize) : 1;
        $nextPage = $requestedPage < $maxPages ? ($requestedPage + 1) : 1;
        $prevPage = $requestedPage === 0 ? 0 : ($nextPage >= 1 ? $requestedPage - 1 : 0);

        return [
            'pages' => (int) $maxPages,
            'next_page' => $nextPage,
            'prev_page' => empty($prevPage) ? 1 : $prevPage,
            'page_size' => $requestedSize,
            'page' => $requestedPage === 0 ? 1 : $requestedPage
        ];
    }
}
