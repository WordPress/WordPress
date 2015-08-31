<?php
/**
 * Class Minify_HTML
 * @package Minify
 */

/**
 * Compress HTML
 *
 * This is a heavy regex-based removal of whitespace, unnecessary comments and
 * tokens. IE conditional comments are preserved. There are also options to have
 * STYLE and SCRIPT blocks compressed by callback functions.
 *
 * A test suite is available.
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_HTML {

    /**
     * "Minify" an HTML page
     *
     * @param string $html
     *
     * @param array $options
     *
     * 'xhtml' : (optional boolean) should content be treated as XHTML1.0? If
     * unset, minify will sniff for an XHTML doctype.
     *
     * @return string
     */
    public static function minify($html, $options = array()) {
        $min = new Minify_HTML($html, $options);
        return $min->process();
    }


    /**
     * Create a minifier object
     *
     * @param string $html
     *
     * @param array $options
     *
     * 'cssMinifier' : (optional) callback function to process content of STYLE
     * elements.
     *
     * 'jsMinifier' : (optional) callback function to process content of SCRIPT
     * elements. Note: the type attribute is ignored.
     *
     * 'xhtml' : (optional boolean) should content be treated as XHTML1.0? If
     * unset, minify will sniff for an XHTML doctype.
     *
     * @return null
     */
    public function __construct($html, $options = array())
    {
        $this->_html = str_replace("\r\n", "\n", trim($html));
        if (isset($options['xhtml'])) {
            $this->_isXhtml = (bool)$options['xhtml'];
        }

        $this->_stripCrlf = (isset($options['stripCrlf']) ? (boolean) $options['stripCrlf'] : false) ;
        $this->_ignoredComments = (isset($options['ignoredComments']) ? (array) $options['ignoredComments'] : array());
    }

    /**
     * Minify the markeup given in the constructor
     *
     * @return string
     */
    public function process()
    {
        if ($this->_isXhtml === null) {
            $this->_isXhtml = (false !== strpos($this->_html, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML'));
        }

        $this->_replacementHash = 'MINIFYHTML' . md5($_SERVER['REQUEST_TIME']);
        $this->_placeholders = array();

        // replace dynamic tags
        $this->_html = preg_replace_callback(
        	'~(<!--\s*m(func|clude)(.*)-->\s*<!--\s*/m(func|clude)\s*-->)~is'
            ,array($this, '_removeComment')
            ,$this->_html);

        // replace SCRIPTs (and minify) with placeholders
        $this->_html = preg_replace_callback(
            '/\\s*(<script\\b[^>]*?>[\\s\\S]*?<\\/script>)\\s*/i'
            ,array($this, '_removeScriptCB')
            ,$this->_html);

        // replace STYLEs (and minify) with placeholders
        $this->_html = preg_replace_callback(
            '/\\s*(<style\\b[^>]*?>[\\s\\S]*?<\\/style>)\\s*/i'
            ,array($this, '_removeStyleCB')
            ,$this->_html);

        // remove HTML comments (not containing IE conditional comments).
        $this->_html = preg_replace_callback(
            '/<!--([\\s\\S]*?)-->/'
            ,array($this, '_commentCB')
            ,$this->_html);

        // replace PREs with placeholders
        $this->_html = preg_replace_callback('/\\s*(<pre\\b[^>]*?>[\\s\\S]*?<\\/pre>)\\s*/i'
            ,array($this, '_removePreCB')
            ,$this->_html);

        // replace TEXTAREAs with placeholders
        $this->_html = preg_replace_callback(
            '/\\s*(<textarea\\b[^>]*?>[\\s\\S]*?<\\/textarea>)\\s*/i'
            ,array($this, '_removeTextareaCB')
            ,$this->_html);

        // trim each line.
        // @todo take into account attribute values that span multiple lines.
        $this->_html = preg_replace('/^\\s+|\\s+$/m', '', $this->_html);

        // remove ws around block/undisplayed elements
        $this->_html = preg_replace('/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
            .'|caption|center|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
            .'|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'
            .'|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
            .'|ul)\\b[^>]*>)/i', '$1', $this->_html);

        // remove ws outside of all elements
        $this->_html = preg_replace_callback(
            '/>([^<]+)</'
            ,array($this, '_outsideTagCB')
            ,$this->_html);

        // use newlines before 1st attribute in open tags (to limit line lengths)
        $this->_html = preg_replace('/(<[a-z\\-]+)\\s+([^>]+>)/i', "$1\n$2", $this->_html);

        if ($this->_stripCrlf) {
            $this->_html = preg_replace("~[\r\n]+~", ' ', $this->_html);
        } else {
            $this->_html = preg_replace("~[\r\n]+~", "\n", $this->_html);
        }

        // fill placeholders
        $this->_html = str_replace(
            array_keys($this->_placeholders)
            ,array_values($this->_placeholders)
            ,$this->_html
        );
        return $this->_html;
    }

    protected function _commentCB($m)
    {
        return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<![') || $this->_ignoredComment($m[1]))
            ? $m[0]
            : '';
    }

    protected function _ignoredComment($comment)
    {
        foreach ($this->_ignoredComments as $ignoredComment) {
            if (stristr($comment, $ignoredComment) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function _reservePlace($content)
    {
        $placeholder = '%' . $this->_replacementHash . count($this->_placeholders) . '%';
        $this->_placeholders[$placeholder] = $content;
        return $placeholder;
    }

    protected $_isXhtml = null;
    protected $_replacementHash = null;
    protected $_placeholders = array();
    protected $_cssMinifier = null;
    protected $_jsMinifier = null;
    protected $_stripCrlf = null;
    protected $_ignoredComments = null;

    protected function _outsideTagCB($m)
    {
        return '>' . preg_replace('/^\\s+|\\s+$/', ' ', $m[1]) . '<';
    }

    protected function _removePreCB($m)
    {
        return $this->_reservePlace($m[1]);
    }

    protected function _removeTextareaCB($m)
    {
        return $this->_reservePlace($m[1]);
    }

    protected function _removeStyleCB($m)
    {
        return $this->_reservePlace($m[1]);
    }

    protected function _removeScriptCB($m)
    {
        return $this->_reservePlace($m[1]);
    }

    protected function _removeCdata($str)
    {
        if (false !== strpos($str, '<![CDATA[')) {
            $str = str_replace('//<![CDATA[', '', $str);
            $str = str_replace('/*<![CDATA[*/', '', $str);
            $str = str_replace('<![CDATA[', '', $str);

            $str = str_replace('//]]>', '', $str);
            $str = str_replace('/*]]>*/', '', $str);
            $str = str_replace(']]>', '', $str);
        }

        return $str;
    }

    protected function _removeComment($m)
    {
        return $this->_reservePlace($m[1]);
    }

    protected function _needsCdata($str)
    {
        return ($this->_isXhtml && preg_match('/(?:[<&]|\\-\\-|\\]\\]>)/', $str));
    }
}
