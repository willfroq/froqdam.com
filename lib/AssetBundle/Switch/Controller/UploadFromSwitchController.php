<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller;

use Froq\AssetBundle\Switch\Action\BuildSwitchUploadRequest;
use Froq\AssetBundle\Switch\Action\CreateTemporaryFile;
use Froq\AssetBundle\Switch\Message\UploadFromSwitch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/upload', name: 'froq_portal_switch.switch_upload', methods: [Request::METHOD_POST])]
final class UploadFromSwitchController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function __invoke(
        Request $request,
        BuildSwitchUploadRequest $buildSwitchUploadRequest,
        MessageBusInterface $messageBus,
        CreateTemporaryFile $createTemporaryFile
    ): JsonResponse {
        $validatedRequest = ($buildSwitchUploadRequest)($request);

        if (count((array) $validatedRequest->errors) > 0) {
            return $this->json(data: ['validationErrors' => $validatedRequest->errors, 'status' => 422], status:  422);
        }

        $messageBus->dispatch(
            new UploadFromSwitch(
                eventName: $validatedRequest->eventName,
                filename: $validatedRequest->filename,
                customerCode: $validatedRequest->customerCode,
                customAssetFolder: $validatedRequest->customAssetFolder,
                assetType: $validatedRequest->assetType,
                assetResourceMetadataFieldCollection: $validatedRequest->assetResourceMetadataFieldCollection,
                productData: $validatedRequest->productData,
                tagData: $validatedRequest->tagData,
                projectData: $validatedRequest->projectData,
                printerData: $validatedRequest->printerData,
                supplierData: $validatedRequest->supplierData,
                temporaryFilePath: ($createTemporaryFile)($request)
            )
        );

        return $this->json(data: [
            'status' => sprintf(
                'File upload for file: %s for customer %s received and is being processed on date: %s . Go to pimcore admin to view the status whether it\'s a success or a failure',
                $validatedRequest->filename, $validatedRequest->customerCode, date('Y-m-d H:i:s')
            )
        ]);
    }
}
