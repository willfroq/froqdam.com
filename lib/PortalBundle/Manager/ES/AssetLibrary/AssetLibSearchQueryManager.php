<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;

class AssetLibSearchQueryManager
{
    /**
     * @param BoolQuery $boolQuery
     * @param LibraryFormDto|null $formDto
     *
     * @return BoolQuery
     */
    public function applySearch(BoolQuery $boolQuery, ?LibraryFormDto $formDto = null): BoolQuery
    {
        $searchTerm = (string)$formDto?->getQuery();

        if (!$searchTerm) {
            return $boolQuery;
        }

        // Check for AND, OR, NOT, &&, ||
        $containsOperator = preg_match('/\b(AND|OR|NOT)\b|(&&|\|\|)/', $searchTerm);

        if (!$containsOperator) {
            // Insert 'AND' where appropriate
            $searchTerm = $this->insertAndOperators($searchTerm);
        }

        $queryString = new QueryString();
        $queryString->setQuery($searchTerm);
        $boolQuery->addMust($queryString);

        return $boolQuery;
    }

    /**
     * Insert 'AND' operators between terms where appropriate.
     *
     * @param string $query
     *
     * @return string
     */
    private function insertAndOperators(string $query): string
    {
        $length = strlen($query);
        $result = '';
        $inPhrase = false;
        $inRange = false;
        $inParens = false;
        $rangeDelimiters = [];
        $parenDepth = 0;
        $prevChar = '';
        $i = 0;

        while ($i < $length) {
            $char = $query[$i];

            // Handle phrases enclosed in double quotes
            if ($char === '"') {
                $inPhrase = !$inPhrase;
                $result .= $char;
                $i++;
                continue;
            }

            // Handle parentheses
            if (!$inPhrase && !$inRange) {
                if ($char === '(') {
                    $parenDepth++;
                    $inParens = true;
                    $result .= $char;
                    $i++;
                    continue;
                } elseif ($char === ')') {
                    $parenDepth--;
                    if ($parenDepth <= 0) {
                        $parenDepth = 0;
                        $inParens = false;
                    }
                    $result .= $char;
                    $i++;
                    continue;
                }
            }

            // Handle ranges enclosed in square brackets or curly braces
            if (!$inPhrase && !$inParens) {
                if ($char === '[' || $char === '{') {
                    array_push($rangeDelimiters, $char);
                    $inRange = true;
                } elseif ($char === ']' || $char === '}') {
                    if (!empty($rangeDelimiters)) {
                        array_pop($rangeDelimiters);
                    }
                    $inRange = !empty($rangeDelimiters);
                }
            }

            if (!$inPhrase && !$inRange && !$inParens && preg_match('/\s/', $char)) {
                // If previous character is not an operator or opening delimiter
                if ($prevChar !== '' && !preg_match('/[\s\(\[\{]/', $prevChar)) {
                    // Add ' AND ' before appending the space
                    $result .= ' AND ';
                }
                // Skip additional spaces
                while ($i < $length && preg_match('/\s/', $query[$i])) {
                    $i++;
                }
                continue;
            } else {
                $result .= $char;
            }

            $prevChar = $char;
            $i++;
        }

        return $result;
    }
}
