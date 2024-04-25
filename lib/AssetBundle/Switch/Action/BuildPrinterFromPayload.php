<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\PrinterFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\AssetBundle\Utility\IsPathExists;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Printer;

final class BuildPrinterFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly IsPathExists $isPathExists,

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

        $printerData = (array) json_decode($switchUploadRequest->printerData, true);

        if (empty($printerData) || ($this->allPropsEmptyOrNull)($printerData)) {
            return;
        }

        $printerFromPayload = new PrinterFromPayload(
            printingProcess: $printerData['printingProcess'] ?? null,
            printingWorkflow: $printerData['printingWorkflow'] ?? null,
            epsonMaterial: $printerData['epsonMaterial'] ?? null,
            substrateMaterial: $printerData['substrateMaterial'] ?? null,
        );

        $printerPath = $rootPrinterFolder.AssetResourceOrganizationFolderNames::Printers->name.'/';
        $printerKey = $printerFromPayload->printingProcess;

        if (!($this->isPathExists)((string) $printerKey, $printerPath)) {
            $printer = new Printer();
            $printer->setPrintingProcess($printerFromPayload->printingProcess);
            $printer->setPrintingWorkflow($printerFromPayload->printingWorkflow);
            $printer->setEpsonMaterial($printerFromPayload->epsonMaterial);
            $printer->setSubstrateMaterial($printerFromPayload->substrateMaterial);

            // TODO Printer, PrintingInks

            $printer->setPublished(true);
            $printer->setParentId((int) $parentPrinterFolder->getId());
            $printer->setKey((string) $printerFromPayload->printingProcess);

            $printer->save();
        }
    }
}
