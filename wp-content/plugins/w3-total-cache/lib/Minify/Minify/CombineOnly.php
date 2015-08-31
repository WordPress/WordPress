<?php

/**
 * Combine only minifier
 */
class Minify_CombineOnly {
    /**
     * Minifies content
     * @param string $content
     * @param array $options
     * @return string
     */
    public static function minify($content, $options = array()) {
        w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/CSS/UriRewriter.php');

        $content = Minify_CSS_UriRewriter::rewrite($content, $options);

        return $content;
    }
}
