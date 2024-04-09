<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - GroupName [input]
 * - Description [textarea]
 * - Users [reverseObjectRelation]
 * - AssetLibraryFilterOptions [block]
 * -- AssetLibraryFilterProperty [select]
 * -- AssetLibraryFilterLabel [input]
 * - AssetLibraryColumnsOptions [block]
 * -- AssetLibraryColumnProperty [select]
 * -- AssetLibraryColumnLabel [input]
 * - AssetLibrarySortOptions [block]
 * -- AssetLibrarySortProperty [select]
 * -- AssetLibrarySortLabel [input]
 * - assetInfoSectionTitle [input]
 * - isAssetInfoSectionEnabled [booleanSelect]
 * - assetInfoSectionItems [structuredTable]
 * - assetInfoSectionMetadata [fieldcollections]
 * - skuSectionTitle [input]
 * - isSKUSectionEnabled [booleanSelect]
 * - skuSectionItems [structuredTable]
 * - skuInfoSectionAttributes [fieldcollections]
 * - projectSectionTitle [input]
 * - isProjectSectionEnabled [booleanSelect]
 * - projectSectionItems [structuredTable]
 * - supplierSectionTitle [input]
 * - isSupplierSectionEnabled [booleanSelect]
 * - supplierSectionItems [structuredTable]
 * - printSectionTitle [input]
 * - isPrintSectionEnabled [booleanSelect]
 * - printSectionItems [structuredTable]
 */

return Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'dao' => NULL,
   'id' => 'group_asset_library_settings',
   'name' => 'GroupAssetLibrarySettings',
   'description' => '',
   'creationDate' => 0,
   'modificationDate' => 1709570291,
   'userOwner' => 2,
   'userModification' => 14,
   'parentClass' => '',
   'implementsInterfaces' => '',
   'listingParentClass' => '',
   'useTraits' => '',
   'listingUseTraits' => '',
   'encryption' => false,
   'encryptedTables' => 
  array (
  ),
   'allowInherit' => false,
   'allowVariants' => false,
   'showVariants' => false,
   'fieldDefinitions' => 
  array (
  ),
   'layoutDefinitions' => 
  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
     'name' => 'pimcore_root',
     'type' => NULL,
     'region' => NULL,
     'title' => NULL,
     'width' => 0,
     'height' => 0,
     'collapsible' => false,
     'collapsed' => false,
     'bodyStyle' => NULL,
     'datatype' => 'layout',
     'permissions' => NULL,
     'children' => 
    array (
      0 => 
      Pimcore\Model\DataObject\ClassDefinition\Layout\Tabpanel::__set_state(array(
         'name' => 'Layout',
         'type' => NULL,
         'region' => NULL,
         'title' => '',
         'width' => '',
         'height' => '',
         'collapsible' => false,
         'collapsed' => false,
         'bodyStyle' => '',
         'datatype' => 'layout',
         'permissions' => NULL,
         'children' => 
        array (
          0 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'name' => 'General',
             'type' => NULL,
             'region' => NULL,
             'title' => 'General',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                 'name' => 'GroupName',
                 'title' => 'Group Name',
                 'tooltip' => '',
                 'mandatory' => true,
                 'noteditable' => false,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'datatype' => 'data',
                 'fieldtype' => 'input',
                 'relationType' => false,
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'width' => '',
                 'defaultValue' => NULL,
                 'columnLength' => 190,
                 'regex' => '',
                 'regexFlags' => 
                array (
                ),
                 'unique' => true,
                 'showCharCount' => false,
                 'defaultValueGenerator' => '',
              )),
              1 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Textarea::__set_state(array(
                 'name' => 'Description',
                 'title' => 'Description',
                 'tooltip' => '',
                 'mandatory' => false,
                 'noteditable' => false,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'datatype' => 'data',
                 'fieldtype' => 'textarea',
                 'relationType' => false,
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'width' => '',
                 'height' => '',
                 'maxLength' => NULL,
                 'showCharCount' => false,
                 'excludeFromSearchIndex' => false,
              )),
              2 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\ReverseObjectRelation::__set_state(array(
                 'name' => 'Users',
                 'title' => 'Users in this group',
                 'tooltip' => '',
                 'mandatory' => false,
                 'noteditable' => false,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'datatype' => 'data',
                 'fieldtype' => 'reverseObjectRelation',
                 'relationType' => true,
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'classes' => 
                array (
                ),
                 'pathFormatterClass' => '',
                 'width' => '',
                 'height' => '',
                 'maxItems' => NULL,
                 'visibleFields' => NULL,
                 'allowToCreateNewObject' => true,
                 'optimizedAdminLoading' => false,
                 'enableTextSelection' => false,
                 'visibleFieldDefinitions' => 
                array (
                ),
                 'ownerClassName' => 'User',
                 'ownerClassId' => NULL,
                 'ownerFieldName' => 'GroupAssetLibrarySettings',
                 'lazyLoading' => true,
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'icon' => '',
             'labelWidth' => 0,
             'labelAlign' => 'left',
          )),
          1 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'name' => 'Search Page',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Search Page',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Layout\Tabpanel::__set_state(array(
                 'name' => 'Layout',
                 'type' => NULL,
                 'region' => NULL,
                 'title' => '',
                 'width' => '',
                 'height' => '',
                 'collapsible' => false,
                 'collapsed' => false,
                 'bodyStyle' => '',
                 'datatype' => 'layout',
                 'permissions' => NULL,
                 'children' => 
                array (
                  0 => 
                  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                     'name' => 'AssetLibraryFilterConfiguration',
                     'type' => NULL,
                     'region' => 'west',
                     'title' => 'Filter Configuration',
                     'width' => '',
                     'height' => '',
                     'collapsible' => false,
                     'collapsed' => false,
                     'bodyStyle' => '',
                     'datatype' => 'layout',
                     'permissions' => NULL,
                     'children' => 
                    array (
                      0 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Block::__set_state(array(
                         'name' => 'AssetLibraryFilterOptions',
                         'title' => 'Filter Options',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'block',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'lazyLoading' => false,
                         'disallowAddRemove' => false,
                         'disallowReorder' => false,
                         'collapsible' => true,
                         'collapsed' => true,
                         'maxItems' => NULL,
                         'styleElement' => '',
                         'children' => 
                        array (
                          0 => 
                          Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
                             'name' => 'AssetLibraryFilterProperty',
                             'title' => 'Filter Property',
                             'tooltip' => '',
                             'mandatory' => true,
                             'noteditable' => false,
                             'index' => false,
                             'locked' => false,
                             'style' => '',
                             'permissions' => NULL,
                             'datatype' => 'data',
                             'fieldtype' => 'select',
                             'relationType' => false,
                             'invisible' => false,
                             'visibleGridView' => false,
                             'visibleSearch' => false,
                             'blockedVarsForExport' => 
                            array (
                            ),
                             'width' => '500px',
                             'defaultValue' => '',
                             'optionsProviderClass' => '@froq.pimcore.options_provider.portal.asset_lib.filter',
                             'optionsProviderData' => '',
                             'columnLength' => 190,
                             'dynamicOptions' => false,
                             'defaultValueGenerator' => '',
                          )),
                          1 => 
                          Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                             'name' => 'AssetLibraryFilterLabel',
                             'title' => 'Filter Label',
                             'tooltip' => '',
                             'mandatory' => false,
                             'noteditable' => false,
                             'index' => false,
                             'locked' => false,
                             'style' => '',
                             'permissions' => NULL,
                             'datatype' => 'data',
                             'fieldtype' => 'input',
                             'relationType' => false,
                             'invisible' => false,
                             'visibleGridView' => false,
                             'visibleSearch' => false,
                             'blockedVarsForExport' => 
                            array (
                            ),
                             'width' => '500px',
                             'defaultValue' => NULL,
                             'columnLength' => 190,
                             'regex' => '',
                             'regexFlags' => 
                            array (
                            ),
                             'unique' => false,
                             'showCharCount' => false,
                             'defaultValueGenerator' => '',
                          )),
                        ),
                         'layout' => NULL,
                         'referencedFields' => 
                        array (
                        ),
                         'fieldDefinitionsCache' => NULL,
                      )),
                    ),
                     'locked' => false,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'fieldtype' => 'panel',
                     'layout' => NULL,
                     'border' => false,
                     'icon' => '',
                     'labelWidth' => 0,
                     'labelAlign' => 'left',
                  )),
                  1 => 
                  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                     'name' => 'AssetLibraryColumnsConfiguration',
                     'type' => NULL,
                     'region' => 'west',
                     'title' => 'Columns Configuration',
                     'width' => '',
                     'height' => '',
                     'collapsible' => false,
                     'collapsed' => false,
                     'bodyStyle' => '',
                     'datatype' => 'layout',
                     'permissions' => NULL,
                     'children' => 
                    array (
                      0 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Block::__set_state(array(
                         'name' => 'AssetLibraryColumnsOptions',
                         'title' => 'Columns Options',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'block',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'lazyLoading' => false,
                         'disallowAddRemove' => false,
                         'disallowReorder' => false,
                         'collapsible' => true,
                         'collapsed' => true,
                         'maxItems' => NULL,
                         'styleElement' => '',
                         'children' => 
                        array (
                          0 => 
                          Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
                             'name' => 'AssetLibraryColumnProperty',
                             'title' => 'Column Property',
                             'tooltip' => '',
                             'mandatory' => true,
                             'noteditable' => false,
                             'index' => false,
                             'locked' => false,
                             'style' => '',
                             'permissions' => NULL,
                             'datatype' => 'data',
                             'fieldtype' => 'select',
                             'relationType' => false,
                             'invisible' => false,
                             'visibleGridView' => false,
                             'visibleSearch' => false,
                             'blockedVarsForExport' => 
                            array (
                            ),
                             'width' => '500px',
                             'defaultValue' => '',
                             'optionsProviderClass' => '@froq.pimcore.options_provider.portal.asset_lib.column',
                             'optionsProviderData' => '',
                             'columnLength' => 190,
                             'dynamicOptions' => false,
                             'defaultValueGenerator' => '',
                          )),
                          1 => 
                          Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                             'name' => 'AssetLibraryColumnLabel',
                             'title' => 'Column Label',
                             'tooltip' => '',
                             'mandatory' => false,
                             'noteditable' => false,
                             'index' => false,
                             'locked' => false,
                             'style' => '',
                             'permissions' => NULL,
                             'datatype' => 'data',
                             'fieldtype' => 'input',
                             'relationType' => false,
                             'invisible' => false,
                             'visibleGridView' => false,
                             'visibleSearch' => false,
                             'blockedVarsForExport' => 
                            array (
                            ),
                             'width' => '',
                             'defaultValue' => NULL,
                             'columnLength' => 190,
                             'regex' => '',
                             'regexFlags' => 
                            array (
                            ),
                             'unique' => false,
                             'showCharCount' => false,
                             'defaultValueGenerator' => '',
                          )),
                        ),
                         'layout' => NULL,
                         'referencedFields' => 
                        array (
                        ),
                         'fieldDefinitionsCache' => NULL,
                      )),
                    ),
                     'locked' => false,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'fieldtype' => 'panel',
                     'layout' => NULL,
                     'border' => false,
                     'icon' => '',
                     'labelWidth' => 0,
                     'labelAlign' => 'left',
                  )),
                  2 => 
                  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                     'name' => 'AssetLibrarySortConfiguration',
                     'type' => NULL,
                     'region' => 'west',
                     'title' => 'Sort Configuration',
                     'width' => '',
                     'height' => '',
                     'collapsible' => false,
                     'collapsed' => false,
                     'bodyStyle' => '',
                     'datatype' => 'layout',
                     'permissions' => NULL,
                     'children' => 
                    array (
                      0 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Block::__set_state(array(
                         'name' => 'AssetLibrarySortOptions',
                         'title' => 'Sort Options',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'block',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'lazyLoading' => false,
                         'disallowAddRemove' => false,
                         'disallowReorder' => false,
                         'collapsible' => true,
                         'collapsed' => true,
                         'maxItems' => NULL,
                         'styleElement' => '',
                         'children' => 
                        array (
                          0 => 
                          Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
                             'name' => 'AssetLibrarySortProperty',
                             'title' => 'Sort Property',
                             'tooltip' => '',
                             'mandatory' => true,
                             'noteditable' => false,
                             'index' => false,
                             'locked' => false,
                             'style' => '',
                             'permissions' => NULL,
                             'datatype' => 'data',
                             'fieldtype' => 'select',
                             'relationType' => false,
                             'invisible' => false,
                             'visibleGridView' => false,
                             'visibleSearch' => false,
                             'blockedVarsForExport' => 
                            array (
                            ),
                             'width' => '500px',
                             'defaultValue' => '',
                             'optionsProviderClass' => '@froq.pimcore.options_provider.portal.asset_lib.sort',
                             'optionsProviderData' => '',
                             'columnLength' => 190,
                             'dynamicOptions' => false,
                             'defaultValueGenerator' => '',
                          )),
                          1 => 
                          Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                             'name' => 'AssetLibrarySortLabel',
                             'title' => 'Sort Label',
                             'tooltip' => '',
                             'mandatory' => false,
                             'noteditable' => false,
                             'index' => false,
                             'locked' => false,
                             'style' => '',
                             'permissions' => NULL,
                             'datatype' => 'data',
                             'fieldtype' => 'input',
                             'relationType' => false,
                             'invisible' => false,
                             'visibleGridView' => false,
                             'visibleSearch' => false,
                             'blockedVarsForExport' => 
                            array (
                            ),
                             'width' => '500px',
                             'defaultValue' => NULL,
                             'columnLength' => 190,
                             'regex' => '',
                             'regexFlags' => 
                            array (
                            ),
                             'unique' => false,
                             'showCharCount' => false,
                             'defaultValueGenerator' => '',
                          )),
                        ),
                         'layout' => NULL,
                         'referencedFields' => 
                        array (
                        ),
                         'fieldDefinitionsCache' => NULL,
                      )),
                    ),
                     'locked' => false,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'fieldtype' => 'panel',
                     'layout' => NULL,
                     'border' => false,
                     'icon' => '',
                     'labelWidth' => 0,
                     'labelAlign' => 'left',
                  )),
                ),
                 'locked' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'fieldtype' => 'tabpanel',
                 'border' => false,
                 'tabPosition' => NULL,
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'icon' => '',
             'labelWidth' => 0,
             'labelAlign' => 'left',
          )),
          2 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'name' => 'Detail Page',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Detail Page',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Layout\Tabpanel::__set_state(array(
                 'name' => 'Layout',
                 'type' => NULL,
                 'region' => NULL,
                 'title' => '',
                 'width' => '',
                 'height' => '',
                 'collapsible' => false,
                 'collapsed' => false,
                 'bodyStyle' => '',
                 'datatype' => 'layout',
                 'permissions' => NULL,
                 'children' => 
                array (
                  0 => 
                  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                     'name' => 'AssetInfoSection',
                     'type' => NULL,
                     'region' => NULL,
                     'title' => 'Asset Info Section',
                     'width' => '',
                     'height' => '',
                     'collapsible' => false,
                     'collapsed' => false,
                     'bodyStyle' => '',
                     'datatype' => 'layout',
                     'permissions' => NULL,
                     'children' => 
                    array (
                      0 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                         'name' => 'assetInfoSectionTitle',
                         'title' => 'Title',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'input',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => '',
                         'defaultValue' => 'Asset information',
                         'columnLength' => 190,
                         'regex' => '',
                         'regexFlags' => 
                        array (
                        ),
                         'unique' => false,
                         'showCharCount' => false,
                         'defaultValueGenerator' => '',
                      )),
                      1 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\BooleanSelect::__set_state(array(
                         'name' => 'isAssetInfoSectionEnabled',
                         'title' => 'Enabled',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'booleanSelect',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'yesLabel' => 'yes',
                         'noLabel' => 'no',
                         'emptyLabel' => 'empty',
                         'options' => 
                        array (
                          0 => 
                          array (
                            'key' => 'empty',
                            'value' => 0,
                          ),
                          1 => 
                          array (
                            'key' => 'yes',
                            'value' => 1,
                          ),
                          2 => 
                          array (
                            'key' => 'no',
                            'value' => -1,
                          ),
                        ),
                         'width' => '',
                      )),
                      2 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\StructuredTable::__set_state(array(
                         'name' => 'assetInfoSectionItems',
                         'title' => 'Items',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'structuredTable',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => 700,
                         'height' => '',
                         'labelWidth' => 255,
                         'labelFirstCell' => '',
                         'cols' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'key' => 'enabled',
                            'label' => 'Enabled',
                            'type' => 'bool',
                            'length' => NULL,
                            'width' => NULL,
                          ),
                          1 => 
                          array (
                            'type' => 'text',
                            'position' => 2,
                            'key' => 'label',
                            'label' => 'Label',
                            'length' => NULL,
                            'width' => 385,
                          ),
                        ),
                         'rows' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'key' => 'asset_type_name',
                            'label' => 'Type',
                          ),
                          1 => 
                          array (
                            'position' => 2,
                            'key' => 'creation_date',
                            'label' => 'Date Added',
                          ),
                          2 => 
                          array (
                            'position' => 3,
                            'key' => 'asset_creation_date',
                            'label' => 'Date Created',
                          ),
                          3 => 
                          array (
                            'position' => 4,
                            'key' => 'last_modified',
                            'label' => 'Last Modified',
                          ),
                          4 => 
                          array (
                            'position' => 5,
                            'key' => 'asset_version',
                            'label' => 'Version',
                          ),
                          5 => 
                          array (
                            'position' => 6,
                            'key' => 'tags',
                            'label' => 'Tags',
                          ),
                        ),
                      )),
                      3 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections::__set_state(array(
                         'name' => 'assetInfoSectionMetadata',
                         'title' => 'Asset Resource Metadata',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'fieldcollections',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'allowedTypes' => 
                        array (
                          0 => 'SettingsMetadata',
                        ),
                         'lazyLoading' => true,
                         'maxItems' => NULL,
                         'disallowAddRemove' => false,
                         'disallowReorder' => false,
                         'collapsed' => false,
                         'collapsible' => false,
                         'border' => false,
                      )),
                    ),
                     'locked' => false,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'fieldtype' => 'panel',
                     'layout' => NULL,
                     'border' => false,
                     'icon' => '',
                     'labelWidth' => 0,
                     'labelAlign' => 'left',
                  )),
                  1 => 
                  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                     'name' => 'skuSection',
                     'type' => NULL,
                     'region' => NULL,
                     'title' => 'SKU Section',
                     'width' => '',
                     'height' => '',
                     'collapsible' => false,
                     'collapsed' => false,
                     'bodyStyle' => '',
                     'datatype' => 'layout',
                     'permissions' => NULL,
                     'children' => 
                    array (
                      0 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                         'name' => 'skuSectionTitle',
                         'title' => 'Title',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'input',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => '',
                         'defaultValue' => 'SKU',
                         'columnLength' => 190,
                         'regex' => '',
                         'regexFlags' => 
                        array (
                        ),
                         'unique' => false,
                         'showCharCount' => false,
                         'defaultValueGenerator' => '',
                      )),
                      1 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\BooleanSelect::__set_state(array(
                         'name' => 'isSKUSectionEnabled',
                         'title' => 'Enabled',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'booleanSelect',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'yesLabel' => 'yes',
                         'noLabel' => 'no',
                         'emptyLabel' => 'empty',
                         'options' => 
                        array (
                          0 => 
                          array (
                            'key' => 'empty',
                            'value' => 0,
                          ),
                          1 => 
                          array (
                            'key' => 'yes',
                            'value' => 1,
                          ),
                          2 => 
                          array (
                            'key' => 'no',
                            'value' => -1,
                          ),
                        ),
                         'width' => '',
                      )),
                      2 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\StructuredTable::__set_state(array(
                         'name' => 'skuSectionItems',
                         'title' => 'Items',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'structuredTable',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => 700,
                         'height' => '',
                         'labelWidth' => 255,
                         'labelFirstCell' => '',
                         'cols' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'key' => 'enabled',
                            'label' => 'Enabled',
                            'type' => 'bool',
                            'length' => NULL,
                            'width' => NULL,
                          ),
                          1 => 
                          array (
                            'type' => 'text',
                            'position' => 2,
                            'key' => 'label',
                            'label' => 'Label',
                            'length' => NULL,
                            'width' => 385,
                          ),
                        ),
                         'rows' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'key' => 'product_name',
                            'label' => 'Name',
                          ),
                          1 => 
                          array (
                            'position' => 2,
                            'key' => 'product_sku',
                            'label' => 'SKU',
                          ),
                          2 => 
                          array (
                            'position' => 3,
                            'key' => 'product_ean',
                            'label' => 'EAN',
                          ),
                          3 => 
                          array (
                            'position' => 4,
                            'key' => 'product_category_segment',
                            'label' => 'Segment',
                          ),
                          4 => 
                          array (
                            'position' => 5,
                            'key' => 'product_category_brand',
                            'label' => 'Brand',
                          ),
                          5 => 
                          array (
                            'position' => 6,
                            'key' => 'product_category_campaign',
                            'label' => 'Campaign',
                          ),
                          6 => 
                          array (
                            'position' => 7,
                            'key' => 'product_category_market',
                            'label' => 'Market',
                          ),
                          7 => 
                          array (
                            'position' => 8,
                            'key' => 'product_category_platform',
                            'label' => 'Platform',
                          ),
                          8 => 
                          array (
                            'position' => 9,
                            'key' => 'net_contents',
                            'label' => 'Net Contents',
                          ),
                          9 => 
                          array (
                            'position' => 10,
                            'key' => 'net_unit_contents',
                            'label' => 'Net Unit Contents',
                          ),
                          10 => 
                          array (
                            'position' => 11,
                            'key' => 'net_content_statement',
                            'label' => 'Net Content Statement',
                          ),
                        ),
                      )),
                      3 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections::__set_state(array(
                         'name' => 'skuInfoSectionAttributes',
                         'title' => 'Sku Info Section Attributes',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'fieldcollections',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'allowedTypes' => 
                        array (
                          0 => 'SettingsMetadata',
                        ),
                         'lazyLoading' => true,
                         'maxItems' => NULL,
                         'disallowAddRemove' => false,
                         'disallowReorder' => false,
                         'collapsed' => false,
                         'collapsible' => false,
                         'border' => false,
                      )),
                    ),
                     'locked' => false,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'fieldtype' => 'panel',
                     'layout' => NULL,
                     'border' => false,
                     'icon' => '',
                     'labelWidth' => 0,
                     'labelAlign' => 'left',
                  )),
                  2 => 
                  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                     'name' => 'projectsSection',
                     'type' => NULL,
                     'region' => NULL,
                     'title' => 'Projects Section',
                     'width' => '',
                     'height' => '',
                     'collapsible' => false,
                     'collapsed' => false,
                     'bodyStyle' => '',
                     'datatype' => 'layout',
                     'permissions' => NULL,
                     'children' => 
                    array (
                      0 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                         'name' => 'projectSectionTitle',
                         'title' => 'Title',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'input',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => '',
                         'defaultValue' => 'Project',
                         'columnLength' => 190,
                         'regex' => '',
                         'regexFlags' => 
                        array (
                        ),
                         'unique' => false,
                         'showCharCount' => false,
                         'defaultValueGenerator' => '',
                      )),
                      1 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\BooleanSelect::__set_state(array(
                         'name' => 'isProjectSectionEnabled',
                         'title' => 'Enabled',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'booleanSelect',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'yesLabel' => 'yes',
                         'noLabel' => 'no',
                         'emptyLabel' => 'empty',
                         'options' => 
                        array (
                          0 => 
                          array (
                            'key' => 'empty',
                            'value' => 0,
                          ),
                          1 => 
                          array (
                            'key' => 'yes',
                            'value' => 1,
                          ),
                          2 => 
                          array (
                            'key' => 'no',
                            'value' => -1,
                          ),
                        ),
                         'width' => '',
                      )),
                      2 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\StructuredTable::__set_state(array(
                         'name' => 'projectSectionItems',
                         'title' => 'Items',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'structuredTable',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => 700,
                         'height' => '',
                         'labelWidth' => 255,
                         'labelFirstCell' => '',
                         'cols' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'key' => 'enabled',
                            'label' => 'Enabled',
                            'type' => 'bool',
                            'length' => NULL,
                            'width' => NULL,
                          ),
                          1 => 
                          array (
                            'type' => 'text',
                            'position' => 2,
                            'key' => 'label',
                            'label' => 'Label',
                            'length' => NULL,
                            'width' => 385,
                          ),
                        ),
                         'rows' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'key' => 'category_managers',
                            'label' => 'Category managers',
                          ),
                          1 => 
                          array (
                            'position' => 2,
                            'label' => 'PIM project nr.',
                            'key' => 'project_pim_project_number',
                          ),
                          2 => 
                          array (
                            'position' => 3,
                            'label' => 'PIM project name',
                            'key' => 'project_name',
                          ),
                          3 => 
                          array (
                            'position' => 4,
                            'label' => 'FroQ project no.',
                            'key' => 'project_froq_project_number',
                          ),
                          4 => 
                          array (
                            'position' => 5,
                            'label' => 'Client',
                            'key' => 'customer',
                          ),
                          5 => 
                          array (
                            'position' => 6,
                            'label' => 'FroQ project name',
                            'key' => 'project_froq_name',
                          ),
                        ),
                      )),
                    ),
                     'locked' => false,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'fieldtype' => 'panel',
                     'layout' => NULL,
                     'border' => false,
                     'icon' => '',
                     'labelWidth' => 0,
                     'labelAlign' => 'left',
                  )),
                  3 => 
                  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                     'name' => 'supplierSection',
                     'type' => NULL,
                     'region' => NULL,
                     'title' => 'Supplier Section',
                     'width' => '',
                     'height' => '',
                     'collapsible' => false,
                     'collapsed' => false,
                     'bodyStyle' => '',
                     'datatype' => 'layout',
                     'permissions' => NULL,
                     'children' => 
                    array (
                      0 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                         'name' => 'supplierSectionTitle',
                         'title' => 'Title',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'input',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => '',
                         'defaultValue' => 'Supplier',
                         'columnLength' => 190,
                         'regex' => '',
                         'regexFlags' => 
                        array (
                        ),
                         'unique' => false,
                         'showCharCount' => false,
                         'defaultValueGenerator' => '',
                      )),
                      1 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\BooleanSelect::__set_state(array(
                         'name' => 'isSupplierSectionEnabled',
                         'title' => 'Enabled',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'booleanSelect',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'yesLabel' => 'yes',
                         'noLabel' => 'no',
                         'emptyLabel' => 'empty',
                         'options' => 
                        array (
                          0 => 
                          array (
                            'key' => 'empty',
                            'value' => 0,
                          ),
                          1 => 
                          array (
                            'key' => 'yes',
                            'value' => 1,
                          ),
                          2 => 
                          array (
                            'key' => 'no',
                            'value' => -1,
                          ),
                        ),
                         'width' => '',
                      )),
                      2 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\StructuredTable::__set_state(array(
                         'name' => 'supplierSectionItems',
                         'title' => 'Items',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'structuredTable',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => 700,
                         'height' => '',
                         'labelWidth' => 255,
                         'labelFirstCell' => '',
                         'cols' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'key' => 'enabled',
                            'label' => 'Enabled',
                            'type' => 'bool',
                            'length' => NULL,
                            'width' => NULL,
                          ),
                          1 => 
                          array (
                            'type' => 'text',
                            'position' => 2,
                            'key' => 'label',
                            'label' => 'Label',
                            'length' => NULL,
                            'width' => 385,
                          ),
                        ),
                         'rows' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'label' => 'Supplier company',
                            'key' => 'supplier_company',
                          ),
                          1 => 
                          array (
                            'position' => 2,
                            'label' => 'Supplier contact',
                            'key' => 'supplier_contact',
                          ),
                          2 => 
                          array (
                            'position' => 3,
                            'label' => 'Streetname + nr.',
                            'key' => 'supplier_street_and_number',
                          ),
                          3 => 
                          array (
                            'position' => 4,
                            'label' => 'Postal code + city',
                            'key' => 'supplier_postal_code_and_city',
                          ),
                          4 => 
                          array (
                            'position' => 5,
                            'label' => 'Phone number',
                            'key' => 'supplier_phone',
                          ),
                          5 => 
                          array (
                            'position' => 6,
                            'label' => 'E-mail',
                            'key' => 'supplier_email',
                          ),
                        ),
                      )),
                    ),
                     'locked' => false,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'fieldtype' => 'panel',
                     'layout' => NULL,
                     'border' => false,
                     'icon' => '',
                     'labelWidth' => 0,
                     'labelAlign' => 'left',
                  )),
                  4 => 
                  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                     'name' => 'printSection',
                     'type' => NULL,
                     'region' => NULL,
                     'title' => 'Print Process Section',
                     'width' => '',
                     'height' => '',
                     'collapsible' => false,
                     'collapsed' => false,
                     'bodyStyle' => '',
                     'datatype' => 'layout',
                     'permissions' => NULL,
                     'children' => 
                    array (
                      0 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                         'name' => 'printSectionTitle',
                         'title' => 'Title',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'input',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => '',
                         'defaultValue' => 'Print Process',
                         'columnLength' => 190,
                         'regex' => '',
                         'regexFlags' => 
                        array (
                        ),
                         'unique' => false,
                         'showCharCount' => false,
                         'defaultValueGenerator' => '',
                      )),
                      1 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\BooleanSelect::__set_state(array(
                         'name' => 'isPrintSectionEnabled',
                         'title' => 'Enabled',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'booleanSelect',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'yesLabel' => 'yes',
                         'noLabel' => 'no',
                         'emptyLabel' => 'empty',
                         'options' => 
                        array (
                          0 => 
                          array (
                            'key' => 'empty',
                            'value' => 0,
                          ),
                          1 => 
                          array (
                            'key' => 'yes',
                            'value' => 1,
                          ),
                          2 => 
                          array (
                            'key' => 'no',
                            'value' => -1,
                          ),
                        ),
                         'width' => '',
                      )),
                      2 => 
                      Pimcore\Model\DataObject\ClassDefinition\Data\StructuredTable::__set_state(array(
                         'name' => 'printSectionItems',
                         'title' => 'Items',
                         'tooltip' => '',
                         'mandatory' => false,
                         'noteditable' => false,
                         'index' => false,
                         'locked' => false,
                         'style' => '',
                         'permissions' => NULL,
                         'datatype' => 'data',
                         'fieldtype' => 'structuredTable',
                         'relationType' => false,
                         'invisible' => false,
                         'visibleGridView' => false,
                         'visibleSearch' => false,
                         'blockedVarsForExport' => 
                        array (
                        ),
                         'width' => 700,
                         'height' => '',
                         'labelWidth' => 255,
                         'labelFirstCell' => '',
                         'cols' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'key' => 'enabled',
                            'label' => 'Enabled',
                            'type' => 'bool',
                            'length' => NULL,
                            'width' => NULL,
                          ),
                          1 => 
                          array (
                            'type' => 'text',
                            'position' => 2,
                            'key' => 'label',
                            'label' => 'Label',
                            'length' => NULL,
                            'width' => 385,
                          ),
                        ),
                         'rows' => 
                        array (
                          0 => 
                          array (
                            'position' => 1,
                            'label' => 'Printing Process',
                            'key' => 'printingprocess',
                          ),
                          1 => 
                          array (
                            'position' => 2,
                            'key' => 'printingworkflow',
                            'label' => 'Printing Workflow',
                          ),
                          2 => 
                          array (
                            'position' => 3,
                            'key' => 'epsonmaterial',
                            'label' => 'Epson Material',
                          ),
                          3 => 
                          array (
                            'position' => 4,
                            'key' => 'substratematerial',
                            'label' => 'Substrate Material',
                          ),
                        ),
                      )),
                    ),
                     'locked' => false,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'fieldtype' => 'panel',
                     'layout' => NULL,
                     'border' => false,
                     'icon' => '',
                     'labelWidth' => 0,
                     'labelAlign' => 'left',
                  )),
                ),
                 'locked' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'fieldtype' => 'tabpanel',
                 'border' => false,
                 'tabPosition' => NULL,
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'icon' => '',
             'labelWidth' => 0,
             'labelAlign' => 'left',
          )),
        ),
         'locked' => false,
         'blockedVarsForExport' => 
        array (
        ),
         'fieldtype' => 'tabpanel',
         'border' => false,
         'tabPosition' => NULL,
      )),
    ),
     'locked' => false,
     'blockedVarsForExport' => 
    array (
    ),
     'fieldtype' => 'panel',
     'layout' => NULL,
     'border' => false,
     'icon' => NULL,
     'labelWidth' => 100,
     'labelAlign' => 'left',
  )),
   'icon' => '',
   'previewUrl' => '',
   'group' => '',
   'showAppLoggerTab' => false,
   'linkGeneratorReference' => '',
   'previewGeneratorReference' => '',
   'compositeIndices' => 
  array (
  ),
   'generateTypeDeclarations' => true,
   'showFieldLookup' => false,
   'propertyVisibility' => 
  array (
    'grid' => 
    array (
      'id' => true,
      'key' => false,
      'path' => true,
      'published' => true,
      'modificationDate' => true,
      'creationDate' => true,
    ),
    'search' => 
    array (
      'id' => true,
      'key' => false,
      'path' => true,
      'published' => true,
      'modificationDate' => true,
      'creationDate' => true,
    ),
  ),
   'enableGridLocking' => false,
   'deletedDataComponents' => 
  array (
  ),
   'blockedVarsForExport' => 
  array (
  ),
   'activeDispatchingEvents' => 
  array (
  ),
));
