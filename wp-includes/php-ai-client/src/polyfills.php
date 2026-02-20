<?php

/**
 * Polyfills for PHP functions that may not be available in older versions.
 *
 * @since 0.1.0
 */
declare (strict_types=1);
namespace WordPress\AiClientDependencies;

if (!\function_exists('array_is_list') && !\function_exists('WordPress\AiClientDependencies\array_is_list')) {
    /**
     * Checks whether a given array is a list.
     *
     * An array is considered a list if its keys consist of consecutive numbers from 0 to count($array)-1.
     *
     * @since 0.1.0
     *
     * @param array<mixed> $array The array to check.
     * @return bool True if the array is a list, false otherwise.
     */
    function array_is_list(array $array): bool
    {
        if ($array === []) {
            return \true;
        }
        $expectedKey = 0;
        foreach (\array_keys($array) as $key) {
            if ($key !== $expectedKey) {
                return \false;
            }
            $expectedKey++;
        }
        return \true;
    }
}
if (!\function_exists('str_starts_with') && !\function_exists('WordPress\AiClientDependencies\str_starts_with')) {
    /**
     * Checks if a string starts with a given substring.
     *
     * @since 0.1.0
     *
     * @param string $haystack The string to search in.
     * @param string $needle The substring to search for.
     * @return bool True if $haystack starts with $needle, false otherwise.
     */
    function str_starts_with(string $haystack, string $needle): bool
    {
        if ('' === $needle) {
            return \true;
        }
        return 0 === \strpos($haystack, $needle);
    }
}
if (!\function_exists('str_contains') && !\function_exists('WordPress\AiClientDependencies\str_contains')) {
    /**
     * Checks if a string contains a given substring.
     *
     * @since 0.1.0
     *
     * @param string $haystack The string to search in.
     * @param string $needle The substring to search for.
     * @return bool True if $haystack contains $needle, false otherwise.
     */
    function str_contains(string $haystack, string $needle): bool
    {
        if ('' === $needle) {
            return \true;
        }
        return \false !== \strpos($haystack, $needle);
    }
}
if (!\function_exists('str_ends_with') && !\function_exists('WordPress\AiClientDependencies\str_ends_with')) {
    /**
     * Checks if a string ends with a given substring.
     *
     * @since 0.1.0
     *
     * @param string $haystack The string to search in.
     * @param string $needle The substring to search for.
     * @return bool True if $haystack ends with $needle, false otherwise.
     */
    function str_ends_with(string $haystack, string $needle): bool
    {
        if ('' === $haystack) {
            return '' === $needle;
        }
        $len = \strlen($needle);
        return \substr($haystack, -$len, $len) === $needle;
    }
}
