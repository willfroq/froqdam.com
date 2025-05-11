<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\Controller;

use Froq\AssetBundle\Export\Action\BuildExportUserGroupCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user-groups', name: 'froq_portal_export.user_groups', methods: [Request::METHOD_GET])]
final class ExportUserGroupsController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, BuildExportUserGroupCollection $buildExportUserCollection): JsonResponse
    {
        $offset = is_numeric($request->query->get('offset')) ? (int) $request->query->get('offset') : 1;
        $limit = is_numeric($request->query->get('limit')) ? (int) $request->query->get('limit') : 5;

        return $this->json(data: ($buildExportUserCollection)($offset, $limit)->toArray());
    }
}
