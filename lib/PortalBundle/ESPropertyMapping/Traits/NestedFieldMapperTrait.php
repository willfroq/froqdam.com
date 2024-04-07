<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping\Traits;

use Froq\PortalBundle\Exception\ES\ESPropertyMappingException;

trait NestedFieldMapperTrait
{
    /**
     * @param object $parent
     * @param string $propertyName
     * @param array<int, mixed> $fields
     *
     * @return array<string|int, mixed>|null
     */
    protected function getNestedFieldValues(object $parent, string $propertyName, array $fields): ?array
    {
        $values = [];
        $current = $parent;
        $fieldsCount = count($fields);
        foreach ($fields as $key => $field) {
            array_shift($fields);
            $fieldGetter = sprintf('get%s', ucfirst($field));
            if (!method_exists($current, $fieldGetter)) {
                throw ESPropertyMappingException::undefinedMethodException($propertyName, $fieldGetter);
            }

            $current = $current->$fieldGetter();
            if (!$current && (($key + 1) < $fieldsCount)) {
                return [];
            }
            if (is_array($current)) {
                foreach ($current as $item) {
                    if (is_object($item)) {
                        $nestedValues = $this->getNestedFieldValues($item, $propertyName, $fields);
                        if ($nestedValues && is_array($nestedValues)) {
                            $values = array_merge($values, $nestedValues);
                        } elseif ($nestedValues && !is_array($nestedValues)) { /** @phpstan-ignore-line */ // This is useless code please test and fix this! - $nestedValues is always false
                            $values[] = $nestedValues;
                        }
                    } else {
                        $values[] = $item;
                    }
                }

                return $values;
            }
        }
        $values[] = $current;

        return $values;
    }
}
