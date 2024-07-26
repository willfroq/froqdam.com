<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig;

use Carbon\Carbon;
use Froq\AssetBundle\Action\GetFileDateFromEmbeddedMetadata;
use Froq\PortalBundle\Contract\PortalDetailExtensionInterface;
use Froq\PortalBundle\Helper\AssetResourceCategoryHelper;
use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Froq\PortalBundle\Helper\StrHelper;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Pimcore\Model\DataObject\ProjectRole;
use Pimcore\Model\DataObject\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PortalDetailExtension extends AbstractExtension implements PortalDetailExtensionInterface
{
    public function __construct(private readonly GetFileDateFromEmbeddedMetadata $getFileDateFromEmbeddedMetadata)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('portal_asset_resource_version', [$this, 'portalAssetResourceVersion']),
            new TwigFunction('portal_asset_resource_product_sku', [$this, 'portalAssetResourceProductSku']),
            new TwigFunction('portal_asset_resource_project_name', [$this, 'portalAssetResourceProjectName']),
            new TwigFunction('portal_asset_resource_file_date_added', [$this, 'portalAssetResourceFileDateAdded']),
            new TwigFunction('portal_asset_resource_file_date_created', [$this, 'portalAssetResourceFileDateCreated']),
            new TwigFunction('portal_asset_resource_file_date_modified', [$this, 'portalAssetResourceFileDateModified']),
            new TwigFunction('portal_product_category_hierarchies', [$this, 'portalProductCategoryHierarchies']),
            new TwigFunction('portal_project_category_managers', [$this, 'portalProjectCategoryManagers']),
            new TwigFunction('portal_pluralize_label', [$this, 'portalPluralizeLabel']),
            new TwigFunction('portal_asset_resource_products', [$this, 'portalAssetResourceProducts']),
            new TwigFunction('portal_asset_resource_projects', [$this, 'portalAssetResourceProjects']),
            new TwigFunction('get_table_row_label_for_asset_detail_config', [AssetDetailSettingsManager::class, 'getAvailableStructuredTableRowLabel']),
            new TwigFunction('get_section_label_for_asset_detail_config', [AssetDetailSettingsManager::class, 'getAvailableSectionLabel']),
            new TwigFunction('portal_asset_resource_product', [$this, 'portalAssetResourceProduct']),
            new TwigFunction('portal_asset_resource_product_ean', [$this, 'portalAssetResourceProductEan']),
            new TwigFunction('portal_asset_resource_product_name', [$this, 'portalAssetResourceProductName']),
            new TwigFunction('portal_asset_resource_froq_project_number', [$this, 'portalAssetResourceFroqProjectNumber']),
            new TwigFunction('portal_asset_resource_pim_project_number', [$this, 'portalAssetResourcePimProjectNumber']),
            new TwigFunction('get_net_contents', [$this, 'getNetContents']),
            new TwigFunction('get_net_unit_contents', [$this, 'getNetUnitContents']),
        ];
    }

    public function getName(): string
    {
        return 'froq_portal_detail_twig_extension';
    }

    public function portalAssetResourceVersion(AssetResource $assetResource): string
    {
        $parent = AssetResourceHierarchyHelper::getSourceAssetResource($assetResource);

        if (AssetResourceHierarchyHelper::isParentWithoutChildren($parent)) {
            // There is no choice but to hard code this cause currently we still show the parent asset resource in search page
            // whenever the asset resource doesn't have children. so the parent will be the first version.
            // but even when we add the first child as version it will still
            // show the same result. So we should think about a fix for this at the end.
            // with current structure the parent is a version as long as it doesn't have any children
            // but the moment the parent has children it is no longer considered as version
            // todo:: So, at the end we should decide whether to consider the parent as a version or not?
            return $parent->getAssetVersion().' of 1';
        }

        $version = $assetResource->getAssetVersion();

        if (is_null($version)) {
            return '';
        }

        $total = AssetResourceHierarchyHelper::getTotalVersionCount($assetResource);

        $result = $version.' of '.$total;

        $latestVersion = AssetResourceHierarchyHelper::getLatestVersion($assetResource);

        if ($assetResource->getId() === $latestVersion->getId()) {
            $result .= ' (newest)';
        }

        return $result;
    }

    public function portalAssetResourceProductSku(AssetResource $assetResource): string
    {
        /** @var ?Product[] $products */
        $products = $this->portalAssetResourceProducts($assetResource);

        if (empty($products)) {
            return '';
        }

        $result = $products[0]->getSKU();

        if (empty($result)) {
            return '';
        }

        $extraCount = count($products) - 1;

        if ($extraCount > 0) {
            $result .= ' <span class="list-item__additional-items-hint">'.$extraCount.' +</span>';
        }

        return $result;
    }

    public function portalAssetResourceProduct(AssetResource $assetResource): ?Product
    {
        /** @var ?Product[] $products */
        $products = $this->portalAssetResourceProducts($assetResource);

        if (empty($products)) {
            return null;
        }

        /** @var ?Product $product */
        $product = current($products);

        if (empty($product)) {
            return null;
        }

        return $product;
    }

    public function portalAssetResourceProductEan(AssetResource $assetResource): string
    {
        /** @var ?Product[] $products */
        $products = $this->portalAssetResourceProducts($assetResource);

        if (empty($products)) {
            return '';
        }

        $result = $products[0]->getEAN();

        if (empty($result)) {
            return '';
        }

        $extraCount = count($products) - 1;

        if ($extraCount > 0) {
            $result .= ' <span class="list-item__additional-items-hint">'.$extraCount.' +</span>';
        }

        return $result;
    }

    public function portalAssetResourceProductName(AssetResource $assetResource): string
    {
        /** @var ?Product[] $products */
        $products = $this->portalAssetResourceProducts($assetResource);

        if (empty($products)) {
            return '';
        }

        $result = $products[0]->getName();

        if (empty($result)) {
            return '';
        }

        $extraCount = count($products) - 1;

        if ($extraCount > 0) {
            $result .= ' <span class="list-item__additional-items-hint">'.$extraCount.' +</span>';
        }

        return $result;
    }

    public function portalAssetResourceFroqProjectNumber(AssetResource $assetResource): string
    {
        /** @var ?Project[] $projects */
        $projects = $this->portalAssetResourceProjects($assetResource);

        if (empty($projects)) {
            return '';
        }

        $result = $projects[0]->getFroq_project_number();

        if (empty($result)) {
            return '';
        }

        $extraCount = count($projects) - 1;

        if ($extraCount > 0) {
            $result .= ' <span class="list-item__additional-items-hint">'.$extraCount.' +</span>';
        }

        return $result;
    }

    public function portalAssetResourcePimProjectNumber(AssetResource $assetResource): string
    {
        /** @var ?Project[] $projects */
        $projects = $this->portalAssetResourceProjects($assetResource);

        if (empty($projects)) {
            return '';
        }

        $result = $projects[0]->getPim_project_number();

        if (empty($result)) {
            return '';
        }

        $extraCount = count($projects) - 1;

        if ($extraCount > 0) {
            $result .= ' <span class="list-item__additional-items-hint">'.$extraCount.' +</span>';
        }

        return $result;
    }

    public function portalAssetResourceProjectName(AssetResource $assetResource): string
    {
        /** @var ?Project[] $projects */
        $projects = $this->portalAssetResourceProjects($assetResource);

        if (empty($projects)) {
            return '';
        }

        $result = $projects[0]->getName();

        if (empty($result)) {
            return '';
        }

        $extraCount = count($projects) - 1;

        if ($extraCount > 0) {
            $result .= ' <span class="list-item__additional-items-hint">'.$extraCount.' +</span>';
        }

        return $result;
    }

    public function portalAssetResourceFileDateAdded(AssetResource $assetResource): string
    {
        return (string) $assetResource->getAsset()?->getCreationDate();
    }

    /**
     * @throws \Exception
     */
    public function portalAssetResourceFileDateCreated(AssetResource $assetResource): ?string
    {
        $createDate = $assetResource->getFileCreateDate()?->format('Y-m-d');

        if (!empty($createDate)) {
            return $createDate;
        }

        $asset = $assetResource->getAsset();

        if (!($asset instanceof Asset)) {
            return null;
        }

        $fileDateCreateDate = (($this->getFileDateFromEmbeddedMetadata)($asset))?->createDate;

        if (empty($assetResource->getFileCreateDate())) {
            $assetResource->setFileCreateDate(new Carbon(time: $fileDateCreateDate));

            $assetResource->save();
        }

        return $fileDateCreateDate;
    }

    /**
     * @throws \Exception
     */
    public function portalAssetResourceFileDateModified(AssetResource $assetResource): ?string
    {
        $modifyDate = $assetResource->getFileModifyDate()?->format('Y-m-d');

        if (!empty($modifyDate)) {
            return $modifyDate;
        }

        $asset = $assetResource->getAsset();

        if (!($asset instanceof Asset)) {
            return null;
        }

        $fileDateModifyDate = (($this->getFileDateFromEmbeddedMetadata)($asset))?->modifyDate;

        if (empty($assetResource->getFileModifyDate())) {
            $assetResource->setFileModifyDate(new Carbon(time: $fileDateModifyDate));

            $assetResource->save();
        }

        return $fileDateModifyDate;
    }

    /**
     * @return array<int|string, array<int|string, string>>
     */
    public function portalProductCategoryHierarchies(Product $product): array
    {
        $categories = $product->getCategories();

        if (empty($categories)) {
            return [];
        }

        return AssetResourceCategoryHelper::getCategoryHierarchies($categories);
    }

    public function portalProjectCategoryManagers(Project $project): string
    {
        $contacts = $project->getContacts() ?? [];

        $categoryManagers = [];

        foreach ($contacts as $contact) {
            /** @var User $person */
            $person = $contact['Person']->getData() ?? null;

            /** @var ProjectRole $role */
            $role = $contact['Role']->getData() ?? null;

            if (empty($person) || empty($role)) {
                continue;
            }

            if (!$person->isPublished() || !$role->isPublished()) {
                continue;
            }

            if ('category_manager' === $role->getName() && !empty($person->getName())) {
                $categoryManagers[] = $person->getName();
            }
        }

        return implode(' / ', array_unique($categoryManagers));
    }

    public function portalPluralizeLabel(string $label, mixed $count): string
    {
        if (is_null($count) || '' === $count) {
            return $label;
        }

        if (is_countable($count)) {
            $count = count($count);
        }

        if ('SKU' === $label && $count > 1) {
            return 'SKUs';
        }

        return StrHelper::plural($label, $count);
    }

    /**
     * @return AbstractObject[]
     */
    public function portalAssetResourceProducts(AssetResource $assetResource): array
    {
        return AssetResourceHierarchyHelper::getSourceAssetResource($assetResource)->getProducts();
    }

    /**
     * @return AbstractObject[]
     */
    public function portalAssetResourceProjects(AssetResource $assetResource): array
    {
        return AssetResourceHierarchyHelper::getSourceAssetResource($assetResource)->getProjects();
    }

    /** @return QuantityValue[] */
    public function getNetContents(Product $product): array
    {
        $fieldCollection = $product->getNetContents();

        $quantityValues = [];

        /** @var ProductContents $productContents */
        foreach ($fieldCollection?->getItems() ?? [] as $productContents) {
            if (!($productContents->getNetContent() instanceof QuantityValue)) {
                continue;
            }

            $quantityValues[] = $productContents->getNetContent();
        }

        return $quantityValues;
    }

    /** @return QuantityValue[] */
    public function getNetUnitContents(Product $product): array
    {
        $fieldCollection = $product->getNetUnitContents();

        $quantityValues = [];

        /** @var ProductContents $productContents */
        foreach ($fieldCollection?->getItems() ?? [] as $productContents) {
            if (!($productContents->getNetContent() instanceof QuantityValue)) {
                continue;
            }

            $quantityValues[] = $productContents->getNetContent();
        }

        return $quantityValues;
    }
}
