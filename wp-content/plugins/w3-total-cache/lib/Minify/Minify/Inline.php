<?php

abstract class Minify_Inline {
    protected $_tag = '';
    protected $_minifier = null;
    protected $_minifierOptions = array();

    abstract static function minify($content, $minifier, $options = array());

    public function setTag($tag) {
        $this->_tag = $tag;
    }

    public function setMinifier($minifier) {
        $this->_minifier = $minifier;
    }

    public function setMinifierOptions($minifierOptions = array()) {
        $this->_minifierOptions = $minifierOptions;
    }

    public function doMinify($content) {
        $search = '/(<' . $this->_tag . '\\b[^>]*?>)([\\s\\S]*?)(<\\/' . $this->_tag . '>)/i';

        $content = preg_replace_callback($search, array($this, '_callback'), $content);

        return $content;
    }

    protected function _callback($match) {
        list(, $openTag, $content, $closeTag) = $match;

        $content = $this->_process($openTag, $content, $closeTag);

        return $content;
    }

    protected function _process($openTag, $content, $closeTag) {
        $content = $this->_removeCdata($content);

        $content = call_user_func($this->_minifier, $content, $this->_minifierOptions);

        if ($this->_needsCdata($content)) {
            $content = $this->_wrapCdata($content);
        }

        $content = $openTag . $content . $closeTag;

        return $content;
    }

    protected function _needsCdata($content) {
        return preg_match('/(?:[<&]|\\-\\-|\\]\\]>)/', $content);
    }

    protected function _removeCdata($content) {
        if (false !== strpos($content, '<![CDATA[')) {
            $content = str_replace('//<![CDATA[', '', $content);
            $content = str_replace('/*<![CDATA[*/', '', $content);
            $content = str_replace('<![CDATA[', '', $content);

            $content = str_replace('//]]>', '', $content);
            $content = str_replace('/*]]>*/', '', $content);
            $content = str_replace(']]>', '', $content);
        }

        return $content;
    }

    protected function _wrapCdata($content) {
        $content = '/*<![CDATA[*/' . $content . '/*]]>*/';

        return $content;
    }
}
