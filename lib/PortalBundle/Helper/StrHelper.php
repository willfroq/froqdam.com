<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Helper;

use Closure;
use Traversable;

class StrHelper
{
    /**
     * Get the portion of a string between two given values.
     */
    public static function between(string $subject, string $from, string $to): string
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return static::beforeLast(static::after($subject, $from), $to);
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     */
    public static function beforeLast(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return substr($subject, 0, $pos);
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     */
    public static function after(string $subject, string $search): string
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Get the plural form of an English word.
     *
     * @param array<int, mixed>|int $count
     */
    public static function plural(string $value, int|array|\Countable $count = 2): string
    {
        return PluralizerHelper::plural($value, $count);
    }

    /**
     * Convert the snake case to pascal case.
     */
    public static function snakeToPascal(?string $str): string
    {
        if (empty($str)) {
            return '';
        }

        return str_replace('_', '', ucwords($str, '_'));
    }

    /**
     * @param array<string, string> $replacements
     * @param array<string, string> $regexReplacements
     */
    public static function hardTrim(?string $str, array $replacements = [], array $regexReplacements = [], ?string $trim = " \n\r\t\v\0"): ?string
    {
        if ($str === '') {
            return '';
        } elseif (is_null($str)) {
            return null;
        }

        if (!empty($replacements)) {
            foreach ($replacements as $search => $replace) {
                $str = self::replace($search, $replace, $str);
            }
        }

        if (!empty($regexReplacements)) {
            foreach ($regexReplacements as $pattern => $replace) {
                $str = self::replaceMatches($pattern, $replace, $str); /** @phpstan-ignore-line */ //typehints here looks wrong, remove the ignore line then run `vendor/bin/phpstan analyse lib --level=8` and please fix it.
            }
        }

        if (!is_null($trim)) {
            $str = trim($str, $trim); /** @phpstan-ignore-line */ //typehints here looks wrong, remove the ignore line then run `vendor/bin/phpstan analyse lib --level=8` and please fix it.
        }

        return $str; /** @phpstan-ignore-line */ //typehints here looks wrong, remove the ignore line then run `vendor/bin/phpstan analyse lib --level=8` and please fix it.
    }

    /**
     * Replace the patterns matching the given regular expression.
     *
     * @param string|string[] $subject
     *
     * @return string|string[]|null
     */
    public static function replaceMatches(
        string $pattern,
        \Closure|string $replace,
        array|string $subject,
        int $limit = -1
    ): string|array|null {
        if ($replace instanceof Closure) {
            return preg_replace_callback($pattern, $replace, $subject, $limit);
        }

        return preg_replace($pattern, $replace, $subject, $limit);
    }

    /**
     * Replace the given value in the given string.
     *
     * @param string|iterable<string> $search
     * @param string|iterable<string> $replace
     * @param string|iterable<string> $subject
     *
     * @return string|string[]
     */
    public static function replace(
        string|iterable $search,
        string|iterable $replace,
        string|iterable $subject,
        bool $caseSensitive = true
    ): string|array {
        if ($search instanceof Traversable) {
            $search = iterator_to_array($search);
        }

        if ($replace instanceof Traversable) {
            $replace = iterator_to_array($replace);
        }

        if ($subject instanceof Traversable) {
            $subject = iterator_to_array($subject);
        }

        return $caseSensitive
            ? str_replace($search, $replace, $subject)
            : str_ireplace($search, $replace, $subject);
    }
}
