<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Printer;

final class BuildPrinterFromPayload
{
    public function __construct(private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull)
    {
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, Organization $organization): void
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

        $printer->setPublished(true);
        $printer->setParentId((int) $parentPrinterFolder->getId());
        $printer->setKey((string) $payload['printingProcess']);

        $printer->save();
    }
}
