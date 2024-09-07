<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Utility;

final class IsLuceneQuery
{
    /**
     * \b(AND|OR|NOT)\b|    # Logical operators
     * [\w\-\.]+:\S+|       # Field-specific queries
     * [+\-]\S+|            # Mandatory/prohibited operators
     * \*\S*|               # Wildcards
     * \[\S+\s+TO\s+\S+\]|  # Range queries
     * \{\S+\s+TO\s+\S+\}|  # Exclusive range queries
     * \~\d?|               # Fuzzy searches
     * \"[^\"]+\"|          # Phrase queries
     * \(|\)|               # Parentheses
     * \s                # Matches any whitespace character (space, tab, newline, etc.)
     */
    public function __invoke(string $searchTerm): bool
    {
        $pattern = '/
            \b(AND|OR|NOT)\b|
            [\w\-\.]+:\S+|
            [+\-]\S+|
            \*\S*|
            \[\S+\s+TO\s+\S+\]|
            \{\S+\s+TO\s+\S+\}|
            \~\d?|
            \"[^\"]+\"|
            \(|\)|
            \s
        /x';

        return preg_match($pattern, $searchTerm) === 1;
    }
}
