<?php
/**
 * Class Minify_Controller_Page
 * @package Minify
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Controller/Base.php');

/**
 * Controller class for serving a single HTML page
 *
 * @link http://code.google.com/p/minify/source/browse/trunk/web/examples/1/index.php#59
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_Controller_Page extends Minify_Controller_Base {

    /**
     * Set up source of HTML content
     *
     * @param array $options controller and Minify options
     * @return array Minify options
     *
     * Controller options:
     *
     * 'content': (required) HTML markup
     *
     * 'id': (required) id of page (string for use in server-side caching)
     *
     * 'lastModifiedTime': timestamp of when this content changed. This
     * is recommended to allow both server and client-side caching.
     *
     * 'minifyAll': should all CSS and Javascript blocks be individually
     * minified? (default false)
     *
     * @todo Add 'file' option to read HTML file.
     */
    public function setupSources($options) {
        if (isset($options['file'])) {
            $sourceSpec = array(
                'filepath' => $options['file']
            );
        } else {
            // strip controller options
            $sourceSpec = array(
                'content' => $options['content']
                ,'id' => $options['id']
            );
            unset($options['content'], $options['id']);
        }
        if (isset($options['minifyAll'])) {
            // this will be the 2nd argument passed to Minify_HTML::minify()
            $sourceSpec['minifyOptions'] = array(
                'cssMinifier' => array('Minify_CSS', 'minify')
                ,'jsMinifier' => array('JSMin', 'minify')
            );
            $this->_loadCssJsMinifiers = true;
            unset($options['minifyAll']);
        }
        $this->sources[] = new Minify_Source($sourceSpec);

        $options['contentType'] = Minify::TYPE_HTML;
        return $options;
    }

    protected $_loadCssJsMinifiers = false;

    /**
     * @see Minify_Controller_Base::loadMinifier()
     */
    public function loadMinifier($minifierCallback)
    {
        if ($this->_loadCssJsMinifiers) {
            // Minify will not call for these so we must manually load
            // them when Minify/HTML.php is called for.
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/CSS.php');
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/JSMin.php');
        }
        parent::loadMinifier($minifierCallback); // load Minify/HTML.php
    }
}

