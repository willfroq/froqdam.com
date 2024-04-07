<?php

declare(strict_types=1);

namespace Froq\PortalBundle\PimcoreOptionsProvider;

use Froq\PortalBundle\Exception\ES\EsFilterOptionsProviderException;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Youwe\PimcoreElasticsearchBundle\Service\IndexListingServiceInterface;

class AssetLibSortOptionsProvider implements SelectOptionsProviderInterface
{
    public function __construct(private readonly IndexListingServiceInterface $esIndexListingManager, private readonly string $esIndexIdAssetLib)
    {
    }

    /**
     * @param array<int, string> $context
     * @param Data $fieldDefinition
     *
     * @return array<int, array<string, string>>
     */
    public function getOptions($context, $fieldDefinition): array
    {
        $options = $this->getKeyValues();
        $this->validate($options);

        return $options;
    }

    /**
     * Returns the value which is defined in the 'Default value' field
     *
     * @param array<int, string> $context
     * @param Data $fieldDefinition
     *
     * @return Data|mixed
     */
    public function getDefaultValue($context, $fieldDefinition): mixed
    {
        return $fieldDefinition->getDefaultValue(); /** @phpstan-ignore-line */
    }

    /**
     * @param array<int, string> $context
     * @param Data $fieldDefinition
     *
     * @return bool
     */
    public function hasStaticOptions($context, $fieldDefinition): bool
    {
        return true;
    }

    /**
     * @param array<int, array<string, string>> $options
     */
    private function validate(array $options): void
    {
        $assetLibraryMapping = $this->esIndexListingManager->getIndex($this->esIndexIdAssetLib)?->getMapping()->getDefinition();
        foreach ($options as $option) {
            if (!isset($assetLibraryMapping[$option['value']])) {
                throw EsFilterOptionsProviderException::undefinedEsMappingException(
                    self::class,
                    $option['value'],
                    $this->esIndexIdAssetLib
                );
            }
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function getKeyValues(): array
    {
        $options = array_filter(AssetLibFilterOptionsProvider::getKeyValues(), function ($option) {
            return !in_array($option['value'], self::getExcludedKeys());
        });

        return array_merge($options, [
            ['key' => 'File Size', 'value' => 'file_size']
        ]);
    }

    /**
     * @return array<int, string>
     */
    private static function getExcludedKeys(): array
    {
        return ['pdf_text'];
    }
}
