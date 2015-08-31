<?php

if (!defined('W3TC')) {
    die();
}

class Minify_Inline_JavaScript extends Minify_Inline {
    public static function minify($content, $minifier, $options = array()) {
        $inline = new self;
        $inline->setTag('script');
        $inline->setMinifier($minifier);
        $inline->setMinifierOptions($options);

        $content = $inline->doMinify($content);

        return $content;
    }

    protected function _process($openTag, $content, $closeTag) {
        $content = preg_replace('/(?:^\\s*(?:\\/\\/)?\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/', '', $content);

        return parent::_process($openTag, $content, $closeTag);
    }
}
