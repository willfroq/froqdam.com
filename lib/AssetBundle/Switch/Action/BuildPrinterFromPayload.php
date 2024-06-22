<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\RelatedObject\CreatePrinterFolder;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\PrinterFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Printer;

final class BuildPrinterFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly CreatePrinterFolder $createPrinterFolder,

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
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Printers->readable())
            ->addConditionParam('o_path = ?', $rootPrinterFolder)
            ->current();

        $printerData = (array) json_decode($switchUploadRequest->printerData, true);

        if (!isset($printerData['printingProcess'])) {
            return;
        }

        if (($this->allPropsEmptyOrNull)($printerData)) {
            return;
        }

        if (!($parentPrinterFolder instanceof DataObject)) {
            $parentPrinterFolder = ($this->createPrinterFolder)($organization, $rootPrinterFolder);
        }

        $printerFromPayload = new PrinterFromPayload(
            printingProcess: $printerData['printingProcess'] ?? null,
            printingWorkflow: $printerData['printingWorkflow'] ?? null,
            epsonMaterial: $printerData['epsonMaterial'] ?? null,
            substrateMaterial: $printerData['substrateMaterial'] ?? null,
        );

        $printerKey = $printerFromPayload->printingProcess;

        $printerPath = $rootPrinterFolder.AssetResourceOrganizationFolderNames::Printers->readable().'/';

        $printer = (new Printer\Listing())
            ->addConditionParam('o_key = ?', $printerKey)
            ->addConditionParam('o_path = ?', $printerPath)
            ->current();

        if (!($printer instanceof Printer)) {
            $printer = new Printer();

            $printer->setPrintingProcess($printerFromPayload->printingProcess);
            $printer->setPrintingWorkflow($printerFromPayload->printingWorkflow);
            $printer->setEpsonMaterial($printerFromPayload->epsonMaterial);
            $printer->setSubstrateMaterial($printerFromPayload->substrateMaterial);
        }

        if (empty($printerFromPayload->printingProcess)) {
            $printer->setPrintingProcess($printerFromPayload->printingProcess);
        }

        if (empty($printerFromPayload->printingWorkflow)) {
            $printer->setPrintingWorkflow($printerFromPayload->printingWorkflow);
        }

        if (empty($printerFromPayload->epsonMaterial)) {
            $printer->setEpsonMaterial($printerFromPayload->epsonMaterial);
        }

        if (empty($printerFromPayload->substrateMaterial)) {
            $printer->setSubstrateMaterial($printerFromPayload->substrateMaterial);
        }

        // TODO Printer, PrintingInks

        if ($parentPrinterFolder instanceof Folder) {
            $printer->setPublished(true);
            $printer->setParentId((int) $parentPrinterFolder->getId());
            $printer->setKey((string) $printerFromPayload->printingProcess);

            $printer->save();
        }
    }
}
