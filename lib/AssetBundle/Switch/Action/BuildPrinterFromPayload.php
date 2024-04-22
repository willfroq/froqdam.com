<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\AssetBundle\Utility\IsPathExists;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Printer;

final class BuildPrinterFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly IsPathExists $isPathExists,
        private readonly ApplicationLogger $logger
    ) {
    }

    /**
     * @param SwitchUploadRequest $switchUploadRequest
     * @param Organization $organization
     * @param array<int, string> $action
     *
     * @throws \Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, Organization $organization, array $action): void
    {
        $rootPrinterFolder = $organization->getObjectFolder() . '/';

        $parentPrinterFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Printers->name)
            ->addConditionParam('o_path = ?', $rootPrinterFolder)
            ->current();

        if (!($parentPrinterFolder instanceof DataObject)) {
            return;
        }

        $payload = (array) json_decode($switchUploadRequest->printerData, true);

        if (empty($payload) || ($this->allPropsEmptyOrNull)($payload)) {
            return;
        }

        $printer = new Printer();

        if (isset($payload['printingProcess'])) {
            $printer->setPrintingProcess($payload['printingProcess']);
        }
        if (isset($payload['printingWorkflow'])) {
            $printer->setPrintingWorkflow($payload['printingWorkflow']);
        }
        if (isset($payload['epsonMaterial'])) {
            $printer->setEpsonMaterial($payload['epsonMaterial']);
        }
        if (isset($payload['substrateMaterial'])) {
            $printer->setSubstrateMaterial($payload['substrateMaterial']);
        }

        // TODO Printer, PrintingInks

        $printerPath = $rootPrinterFolder.AssetResourceOrganizationFolderNames::Printers->name;

        if (($this->isPathExists)($switchUploadRequest, $printerPath)) {
            $message = sprintf('Related printer NOT created. %s path already exists, this has to be unique.', $printerPath);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);
        }

        if (!($this->isPathExists)($switchUploadRequest, $printerPath)) {
            $printer->setPublished(true);
            $printer->setParentId((int) $parentPrinterFolder->getId());
            $printer->setKey((string) $payload['printingProcess']);

            $printer->save();
        }
    }
}
