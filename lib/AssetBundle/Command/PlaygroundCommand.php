<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Command;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Action\BuildAssetResourceMetadata;
use Froq\AssetBundle\Switch\Action\BuildPrinterFromPayload;
use Froq\AssetBundle\Switch\Action\BuildProductFromPayload;
use Froq\AssetBundle\Switch\Action\BuildProjectFromPayload;
use Froq\AssetBundle\Switch\Action\BuildSupplierFromPayload;
use Froq\AssetBundle\Switch\Action\BuildTags;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsCommand(
    name: 'test:anything',
    description: 'hi.',
    aliases: ['test:anything'],
    hidden: false
)]
class PlaygroundCommand extends AbstractCommand
{
    public function __construct(
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 1;
    }

    //    /**
    //     * @throws \Exception
    //     */
    //    private function assetResourceMetadataTest(): void
    //    {
    //        $switchUploadRequest = new SwitchUploadRequest(
    //            eventName: 'upload',
    //            filename: 'test.pdf',
    //            customerCode: '',
    //            customAssetFolder: '',
    //            assetType: '',
    //            fileContents: new UploadedFile(
    //                $this->kernel->getProjectDir().'/lib/AssetBundle/Command/file/test.pdf',
    //                'test.pdf'
    //            ),
    //            assetResourceMetadataFieldCollection: '[{"key1": "hi"}, {"key1": "hi"}, {"key1": "hi"}]',
    //            productData: '',
    //            tagData: '',
    //            projectData: '',
    //            printerData: '',
    //            supplierData: '',
    //            errors: [],
    //        );
    //
    //        $assetResourceMetadataFieldCollection = ($this->buildAssetResourceMetadata)($switchUploadRequest);
    //
    //        $asset = Asset::getById(7851);
    //        $assetType = AssetType::getById(92);
    //
    //        $assetResourceLatestVersion = AssetResource::create();
    //        $assetResourceLatestVersion->setPublished(true);
    //        $assetResourceLatestVersion->setPath('test/');
    //        $assetResourceLatestVersion->setName('test.pdf');
    //        $assetResourceLatestVersion->setParentId(8);
    //        $assetResourceLatestVersion->setAsset($asset);
    //        $assetResourceLatestVersion->setAssetType($assetType);
    //        $assetResourceLatestVersion->setAssetVersion(0);
    //        $assetResourceLatestVersion->setKey('test.pdf');
    //        $assetResourceLatestVersion->setMetadata($assetResourceMetadataFieldCollection);
    //
    //        $assetResourceLatestVersion->save();
    //    }
    //
    //    /**
    //     * @throws Exception
    //     * @throws \Doctrine\DBAL\Driver\Exception
    //     * @throws \Exception
    //     */
    //    private function assetResourceProductCategoryTest(): void
    //    {
    //        $switchUploadRequest = new SwitchUploadRequest(
    //            eventName: 'upload',
    //            filename: 'test.pdf',
    //            customerCode: '99',
    //            customAssetFolder: '',
    //            assetType: '',
    //            fileContents: new UploadedFile(
    //                $this->kernel->getProjectDir().'/tests/Fixtures/file/test.pdf',
    //                'test.pdf'
    //            ),
    //            assetResourceMetadataFieldCollection: '',
    //            productData: '{
    //        "productName": "productName",
    //        "productEAN": "productEAN",
    //        "productSKU": "productSKU",
    //        "productAttributes": [
    //            {"key1": "value1"},
    //            {"key2": "value2"}
    //        ],
    //        "productNetContentStatement": "productNetContentStatement",
    //        "productNetContents": {"value": "1800", "attribute": "ml"},
    //        "productNetUnitContents": {"value": "300", "attribute": "ml"},
    //        "productCategories": {
    //            "brand": "brand",
    //            "campaign": "campaign",
    //            "market": "market",
    //            "segment": "segment",
    //            "platform": "platform"
    //        }
    //    }',
    //            tagData: '',
    //            projectData: '',
    //            printerData: '',
    //            supplierData: '',
    //            errors: [],
    //        );
    //
    //        $asset = Asset::getById(7851);
    //        $assetType = AssetType::getById(92);
    //        $organization = Organization::getById(39);
    //
    //        $assetResourceLatestVersion = AssetResource::create();
    //        $assetResourceLatestVersion->setPublished(true);
    //        $assetResourceLatestVersion->setPath('test/');
    //        $assetResourceLatestVersion->setName('test2.pdf');
    //        $assetResourceLatestVersion->setParentId(8);
    //        $assetResourceLatestVersion->setAsset($asset);
    //        $assetResourceLatestVersion->setAssetType($assetType);
    //        $assetResourceLatestVersion->setAssetVersion(0);
    //        $assetResourceLatestVersion->setKey('test2.pdf');
    //        $assetResourceLatestVersion->setMetadata(null);
    //
    //        $assetResourceLatestVersion->save();
    //
    //        //        ($this->buildProductFromPayload)($switchUploadRequest, [$assetResourceLatestVersion], $organization, []);
    //        $assetResourceLatestVersion->delete();
    //
    //        dd($assetResourceLatestVersion);
    //    }
    //
    //    private function tagTest()
    //    {
    //        $switchUploadRequest = new SwitchUploadRequest(
    //            eventName: 'upload',
    //            filename: 'test.pdf',
    //            customerCode: '',
    //            customAssetFolder: '',
    //            assetType: '',
    //            fileContents: new UploadedFile(
    //                $this->kernel->getProjectDir().'/lib/AssetBundle/Command/file/test.pdf',
    //                'test.pdf'
    //            ),
    //            assetResourceMetadataFieldCollection: '',
    //            productData: '',
    //            tagData: '
    //            [
    //        {"code": "tag1", "name": "name"},
    //        {"code": "tag2", "name": "name"},
    //    ]
    //            ',
    //            projectData: '',
    //            printerData: '',
    //            supplierData: '',
    //            errors: [],
    //        );
    //
    //        $asset = Asset::getById(7851);
    //        $assetType = AssetType::getById(92);
    //        $organization = Organization::getById(39);
    //
    //        $assetResourceLatestVersion = AssetResource::create();
    //        $assetResourceLatestVersion->setPublished(true);
    //        $assetResourceLatestVersion->setPath('test/');
    //        $assetResourceLatestVersion->setName('test.pdf');
    //        $assetResourceLatestVersion->setParentId(8);
    //        $assetResourceLatestVersion->setAsset($asset);
    //        $assetResourceLatestVersion->setAssetType($assetType);
    //        $assetResourceLatestVersion->setAssetVersion(0);
    //        $assetResourceLatestVersion->setKey('test.pdf');
    //
    //        ($this->buildTags)($switchUploadRequest, $assetResourceLatestVersion, $organization);
    //
    //        $assetResourceLatestVersion->save();
    //    }
    //
    //    private function projectTest()
    //    {
    //        $switchUploadRequest = new SwitchUploadRequest(
    //            eventName: 'upload',
    //            filename: 'test.pdf',
    //            customerCode: '99',
    //            customAssetFolder: '',
    //            assetType: '',
    //            fileContents: new UploadedFile(
    //                $this->kernel->getProjectDir().'/tests/Fixtures/file/test.pdf',
    //                'test.pdf'
    //            ),
    //            assetResourceMetadataFieldCollection: '',
    //            productData: '',
    //            tagData: '',
    //            projectData: '{
    //                "projectCode": "projectCode",
    //	            "projectName": "projectName",
    //	            "pimProjectNumber": "pimProjectNumber",
    //	            "froqProjectNumber": "froqProjectNumber",
    //	            "customerProjectNumber": "customerProjectNumber",
    //	            "froqName": "froqName",
    //	            "description": "description",
    //	            "projectType": "projectType",
    //	            "status": "status",
    //	            "location": "location",
    //	            "deliveryType": "deliveryType"
    //            }',
    //            printerData: '',
    //            supplierData: '',
    //            errors: [],
    //        );
    //
    //        $asset = Asset::getById(7851);
    //        $assetType = AssetType::getById(92);
    //        $organization = Organization::getById(39);
    //
    //        $assetResourceLatestVersion = AssetResource::create();
    //        $assetResourceLatestVersion->setPublished(true);
    //        $assetResourceLatestVersion->setPath('test/');
    //        $assetResourceLatestVersion->setName('test.pdf');
    //        $assetResourceLatestVersion->setParentId(8);
    //        $assetResourceLatestVersion->setAsset($asset);
    //        $assetResourceLatestVersion->setAssetType($assetType);
    //        $assetResourceLatestVersion->setAssetVersion(0);
    //        $assetResourceLatestVersion->setKey('test.pdf');
    //        $assetResourceLatestVersion->setMetadata(null);
    //        $assetResourceLatestVersion->save();
    //
    //        ($this->buildProjectFromPayload)($switchUploadRequest, $assetResourceLatestVersion, $organization);
    //    }
    //
    //    private function supplierProjectTest()
    //    {
    //        $switchUploadRequest = new SwitchUploadRequest(
    //            eventName: 'upload',
    //            filename: 'test.pdf',
    //            customerCode: '',
    //            customAssetFolder: '',
    //            assetType: '',
    //            fileContents: new UploadedFile(
    //                $this->kernel->getProjectDir().'/tests/Fixtures/file/test.pdf',
    //                'test.pdf'
    //            ),
    //            assetResourceMetadataFieldCollection: '',
    //            productData: '',
    //            tagData: '',
    //            projectData: '',
    //            printerData: '
    //                {
    //        "printingProcess": "printingProcess",
    //        "printingWorkflow": "printingWorkflow",
    //        "epsonMaterial": "epsonMaterial",
    //        "substrateMaterial": "substrateMaterial"
    //    }
    //            ',
    //            supplierData: '
    //                {
    //        "supplierCode": "supplierCode",
    //        "supplierCompany": "supplierCompany",
    //        "supplierContact": "supplierContact",
    //        "supplierStreetName": "supplierStreetName",
    //        "supplierStreetNumber": "supplierStreetNumber",
    //        "supplierPostalCode": "supplierPostalCode",
    //        "supplierCity": "supplierCity",
    //        "supplierPhoneNumber": "supplierPhoneNumber",
    //        "supplierEmail": "supplierEmail@da.ml"
    //    }
    //            ',
    //            errors: [],
    //        );
    //
    //        $organization = Organization::getById(39);
    //
    //        ($this->buildPrinterFromPayload)($switchUploadRequest, $organization);
    //        ($this->buildSupplierFromPayload)($switchUploadRequest, $organization);
    //    }
}
