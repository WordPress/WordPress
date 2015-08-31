<?php

if (!function_exists('file_put_contents')) {
    if (!defined('FILE_APPEND')) {
        define('FILE_APPEND', 8);
    }

    function file_put_contents($filename, $data, $flags = 0) {
        $fp = fopen($filename, ($flags & FILE_APPEND ? 'a' : 'w'));

        if ($fp) {
            fputs($fp, $data);
            fclose($fp);

            return true;
        }

        return false;
    }
}

if (!function_exists('fnmatch')) {
    define('FNM_PATHNAME', 1);
    define('FNM_NOESCAPE', 2);
    define('FNM_PERIOD', 4);
    define('FNM_CASEFOLD', 16);

    function fnmatch($pattern, $string, $flags = 0) {
        $modifiers = null;
        $transforms = array(
            '\*' => '.*',
            '\?' => '.',
            '\[\!' => '[^',
            '\[' => '[',
            '\]' => ']',
            '\.' => '\.',
            '\\' => '\\\\'
        );

        // Forward slash in string must be in pattern:
        if ($flags & FNM_PATHNAME) {
            $transforms['\*'] = '[^/]*';
        }

        // Back slash should not be escaped:
        if ($flags & FNM_NOESCAPE) {
            unset($transforms['\\']);
        }

        // Perform case insensitive match:
        if ($flags & FNM_CASEFOLD) {
            $modifiers .= 'i';
        }

        // Period at start must be the same as pattern:
        if ($flags & FNM_PERIOD) {
            if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0) return false;
        }

        $pattern = '~^' . strtr(preg_quote($pattern, '~'), $transforms) . '$~' . $modifiers;

        return (boolean) preg_match($pattern, $string);
    }
}
function w3tc_get_theme($themename) {
    global $wp_version;
    if (version_compare($wp_version,'3.4', '<'))
        return get_theme($themename);

    $wp_themes = w3tc_get_themes();


    if ( is_array( $wp_themes ) && array_key_exists( $themename, $wp_themes ) )
        return $wp_themes[ $themename ];
    return array();
}
function w3tc_get_current_theme_name() {
    global $wp_version;
    if (version_compare($wp_version,'3.4', '>='))
        return wp_get_theme()->get('Name');
    return get_current_theme();
}

function w3tc_get_current_theme() {
    global $wp_version;
    if (version_compare($wp_version,'3.4', '>='))
        return wp_get_theme();
    return get_theme(get_current_theme());
}

function w3tc_get_themes() {
    global $wp_version;
    if (version_compare($wp_version,'3.4', '<'))
        return get_themes();

    global $wp_themes;
    if ( isset( $wp_themes ) )
        return $wp_themes;

    $themes = wp_get_themes();
    $wp_themes = array();

    foreach ( $themes as $theme ) {
        $name = $theme->get('Name');
        if ( isset( $wp_themes[ $name ] ) )
            $wp_themes[ $name . '/' . $theme->get_stylesheet() ] = $theme;
        else
            $wp_themes[ $name ] = $theme;
    }

    return $wp_themes;
}