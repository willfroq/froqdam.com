<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action;

use Froq\PortalBundle\DTO\QueryResponseDto;
use Symfony\Component\HttpFoundation\Request;

final class GetPaginator
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(Request $request, QueryResponseDto $queryResponseDto, int $defaultPageSize = 24): array
    {
        $page = $request->get('page', 1);
        $size = $request->get('size', $defaultPageSize);
        $totalItems = $queryResponseDto->getTotalCount();
        $maxPages = ($totalItems > 0) ? ceil($totalItems / $size) : 1;
        $nextPage = $page < $maxPages ? ($page + 1) : false;

        return [
            'pages' => $maxPages,
            'next_page' => $nextPage
        ];
    }
}
