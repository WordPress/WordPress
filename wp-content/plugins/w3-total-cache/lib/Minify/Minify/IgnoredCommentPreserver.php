<?php

class Minify_IgnoredCommentPreserver {
    protected $_replacementHash = '';
    protected $_ignoredComments = array();
    protected $_placeholders = array();

    public function __construct() {
        $this->_replacementHash = 'IgnoredCommentPreserver_' . md5(time());
    }

    public function setIgnoredComments($ignoredComments = array()) {
        $this->_ignoredComments = $ignoredComments;
    }

    public function search(&$html) {
        $html = preg_replace_callback('/<!--[\\s\\S]*?-->/', array($this, '_callback'), $html);
    }

    public function replace(&$html) {
        $html = str_replace(array_keys($this->_placeholders), array_values($this->_placeholders), $html);
    }

    protected function _callback($match) {
        list($comment) = $match;

        if ($this->_isIgnoredComment($comment)) {
            return $this->_reservePlace($comment);
        }

        return $comment;
    }

    protected function _isIgnoredComment(&$comment) {
        foreach ($this->_ignoredComments as $ignoredComment) {
            if (stristr($comment, $ignoredComment) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function _getPlaceholder() {
        return '%%' . $this->_replacementHash . '_' . count($this->_placeholders) . '%%';
    }

    protected function _reservePlace(&$content) {
        $placeholder = $this->_getPlaceholder();

        $this->_placeholders[$placeholder] = &$content;

        return $placeholder;
    }
}
