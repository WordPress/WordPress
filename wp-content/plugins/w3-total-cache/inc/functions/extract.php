<?php

/**
 * Extracts JS files from content
 *
 * @param string $content
 * @return array
 */
function w3_extract_js($content) {
    $matches = null;
    $files = array();

    $content = preg_replace('~<!--.*?-->~s', '', $content);

    if (preg_match_all('~<script\s+[^<>]*src=["\']?([^"\']+)["\']?[^<>]*>\s*</script>~is', $content, $matches)) {
        $files = $matches[1];
    }

    $files = array_values(array_unique($files));

    return $files;
}

/**
 * Extract CSS files from content
 *
 * @param string $content
 * @return array
 */
function w3_extract_css($content) {
    $matches = null;
    $files = array();

    $content = preg_replace('~<!--.*?-->~s', '', $content);

    if (preg_match_all('~<link\s+([^>]+)/?>(.*</link>)?~Uis', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attrs = array();
            $attr_matches = null;

            if (preg_match_all('~(\w+)=["\']([^"\']*)["\']~', $match[1], $attr_matches, PREG_SET_ORDER)) {
                foreach ($attr_matches as $attr_match) {
                    $attrs[$attr_match[1]] = trim($attr_match[2]);
                }
            }

            if (isset($attrs['href']) && isset($attrs['rel']) && stristr($attrs['rel'], 'stylesheet') !== false && (!isset($attrs['media']) || stristr($attrs['media'], 'print') === false)) {
                $files[] = $attrs['href'];
            }
        }
    }

    if (preg_match_all('~@import\s+(url\s*)?\(?["\']?\s*([^"\'\)\s]+)\s*["\']?\)?[^;]*;?~is', $content, $matches)) {
        $files = array_merge($files, $matches[2]);
    }

    $files = array_values(array_unique($files));

    return $files;
}

function w3_extract_css2($content) {
    $matches = null;
    $files = array();

    $content = preg_replace('~<!--.*?-->~s', '', $content);

    if (preg_match_all('~<link\s+([^>]+)/?>(.*</link>)?~Uis', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attrs = array();
            $attr_matches = null;
            if (preg_match_all('~(\w+)=["\']([^"\']*)["\']~', $match[1], $attr_matches, PREG_SET_ORDER)) {
                foreach ($attr_matches as $attr_match) {
                    $attrs[$attr_match[1]] = trim($attr_match[2]);
                }
            }

            if (isset($attrs['href']) && isset($attrs['rel']) && stristr($attrs['rel'], 'stylesheet') !== false && (!isset($attrs['media']) || stristr($attrs['media'], 'print') === false)) {
                $files[] = array($match[0], $attrs['href']);
            }
        }

    }

    if (preg_match_all('~@import\s+(url\s*)?\(?["\']?\s*([^"\'\)\s]+)\s*["\']?\)?[^;]*;?~is', $content, $matches, PREG_SET_ORDER)) {
        foreach($matches as $match)
            $files[] = array($match[0],$match[2]);
    }

    return $files;
}