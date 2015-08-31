<?php

class Minify_HTMLTidy {
    public static function minify($content, $options = array()) {
        $options = array_merge(array(
            'clean' => false,
            'hide-comments' => true,
            'wrap' => 0,
            'input-encoding' => 'utf8',
            'output-encoding' => 'utf8',
            'preserve-entities' => true
        ), $options, array(
            'show-errors' => 0,
            'show-warnings' => false,
            'force-output' => true,
            'tidy-mark' => false
        ));

        $tidy = new tidy();
        $tidy->parseString($content, $options);
        $tidy->cleanRepair();

        $content = $tidy->value;

        return $content;
    }

    public static function minifyXhtml($html, $options = array()) {
        $options = array_merge($options, array(
            'output-xhtml' => true
        ));

        return self::minify($html, $options);
    }

    public static function minifyXml($xml, $options = array()) {
        $options = array_merge($options, array(
            'input-xml' => true,
            'output-xml' => true,
            'add-xml-decl' => true
        ));

        return self::minify($xml, $options);
    }
}
