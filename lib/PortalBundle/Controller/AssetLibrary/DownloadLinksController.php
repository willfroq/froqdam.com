<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\AssetLibrary;

use Froq\PortalBundle\Action\Request\BuildDownloadLinksRequest;
use Froq\PortalBundle\Action\Response\GenerateDownloadAssetsUrl;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/download-links', name: 'froq_portal.asset_library.download.links')]
final class DownloadLinksController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(
        Request $request,
        BuildDownloadLinksRequest $buildDownloadLinksRequest,
        GenerateDownloadAssetsUrl $generateDownloadAssetsUrl,
    ): Response {
        $currentUser = $this->getUser();

        if (!($currentUser instanceof User)) {
            throw $this->createAccessDeniedException();
        }

        $validatedRequest = ($buildDownloadLinksRequest)($request, $currentUser);

        if (count((array) $validatedRequest->errors) > 0) {
            return $this->json(data: ['validationErrors' => $validatedRequest->errors, 'status' => 422], status:  422);
        }

        return $this->json(data: ['publicUrl' => ($generateDownloadAssetsUrl)($validatedRequest)]);
    }
}
