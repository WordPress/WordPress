<?php

namespace ElementorDeps;

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ElementorDeps\Twig\Environment;
use ElementorDeps\Twig\Extension\CoreExtension;
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_cycle($values, $position)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::cycle($values, $position);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_random(Environment $env, $values = null, $max = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::random($env->getCharset(), $values, $max);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_date_format_filter(Environment $env, $date, $format = null, $timezone = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return $env->getExtension(CoreExtension::class)->formatDate($date, $format, $timezone);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_date_modify_filter(Environment $env, $date, $modifier)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return $env->getExtension(CoreExtension::class)->modifyDate($date, $modifier);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_sprintf($format, ...$values)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::sprintf($format, ...$values);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_date_converter(Environment $env, $date = null, $timezone = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return $env->getExtension(CoreExtension::class)->convertDate($date, $timezone);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_replace_filter($str, $from)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::replace($str, $from);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_round($value, $precision = 0, $method = 'common')
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::round($value, $precision, $method);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_number_format_filter(Environment $env, $number, $decimal = null, $decimalPoint = null, $thousandSep = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return $env->getExtension(CoreExtension::class)->formatNumber($number, $decimal, $decimalPoint, $thousandSep);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_urlencode_filter($url)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::urlencode($url);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_array_merge(...$arrays)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::merge(...$arrays);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_slice(Environment $env, $item, $start, $length = null, $preserveKeys = \false)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::slice($env->getCharset(), $item, $start, $length, $preserveKeys);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_first(Environment $env, $item)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::first($env->getCharset(), $item);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_last(Environment $env, $item)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::last($env->getCharset(), $item);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_join_filter($value, $glue = '', $and = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::join($value, $glue, $and);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_split_filter(Environment $env, $value, $delimiter, $limit = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::split($env->getCharset(), $value, $delimiter, $limit);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_get_array_keys_filter($array)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::keys($array);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_reverse_filter(Environment $env, $item, $preserveKeys = \false)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::reverse($env->getCharset(), $item, $preserveKeys);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_sort_filter(Environment $env, $array, $arrow = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::sort($env, $array, $arrow);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_matches(string $regexp, ?string $str)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::matches($regexp, $str);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_trim_filter($string, $characterMask = null, $side = 'both')
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::trim($string, $characterMask, $side);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_nl2br($string)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::nl2br($string);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_spaceless($content)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::spaceless($content);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_convert_encoding($string, $to, $from)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::convertEncoding($string, $to, $from);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_length_filter(Environment $env, $thing)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::length($env->getCharset(), $thing);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_upper_filter(Environment $env, $string)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::upper($env->getCharset(), $string);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_lower_filter(Environment $env, $string)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::lower($env->getCharset(), $string);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_striptags($string, $allowable_tags = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::striptags($string, $allowable_tags);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_title_string_filter(Environment $env, $string)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::titleCase($env->getCharset(), $string);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_capitalize_string_filter(Environment $env, $string)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::capitalize($env->getCharset(), $string);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_test_empty($value)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::testEmpty($value);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_test_iterable($value)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return \is_iterable($value);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_include(Environment $env, $context, $template, $variables = [], $withContext = \true, $ignoreMissing = \false, $sandboxed = \false)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_source(Environment $env, $name, $ignoreMissing = \false)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::source($env, $name, $ignoreMissing);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_constant($constant, $object = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::constant($constant, $object);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_constant_is_defined($constant, $object = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::constantIsDefined($constant, $object);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_array_batch($items, $size, $fill = null, $preserveKeys = \true)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::batch($items, $size, $fill, $preserveKeys);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_array_column($array, $name, $index = null) : array
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::column($array, $name, $index);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_array_filter(Environment $env, $array, $arrow)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::filter($env, $array, $arrow);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_array_map(Environment $env, $array, $arrow)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::map($env, $array, $arrow);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_array_reduce(Environment $env, $array, $arrow, $initial = null)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::reduce($env, $array, $arrow, $initial);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_array_some(Environment $env, $array, $arrow)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::arraySome($env, $array, $arrow);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_array_every(Environment $env, $array, $arrow)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::arrayEvery($env, $array, $arrow);
}
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_check_arrow_in_sandbox(Environment $env, $arrow, $thing, $type)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    return CoreExtension::checkArrowInSandbox($env, $arrow, $thing, $type);
}
