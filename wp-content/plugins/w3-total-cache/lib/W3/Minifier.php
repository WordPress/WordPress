<?php

/**
 * Minifiers object
 */

/**
 * Class W3_Minifier
 */
class W3_Minifier {
    /**
     * Config instance
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * Minifiers array
     *
     * @var array
     */
    var $_minifiers = array(
        'combinejs' => array('Minify_CombineOnly', 'minify'),
        'combinecss' => array('Minify_CombineOnly', 'minify'),

        'js' => array('JSMin', 'minify'),
        'yuijs' => array('Minify_YUICompressor', 'minifyJs'),
        'ccjs' => array('Minify_ClosureCompiler', 'minify'),

        'css' => array('Minify_CSS', 'minify'),
        'yuicss' => array('Minify_YUICompressor', 'minifyCss'),
        'csstidy' => array('Minify_CSSTidy', 'minify'),

        'html' => array('Minify_HTML', 'minify'),
        'htmlxml' => array('Minify_HTML', 'minify'),

        'htmltidy' => array('Minify_HTMLTidy', 'minifyXhtml'),
        'htmltidyxml' => array('Minify_HTMLTidy', 'minifyXml')
    );

    /**
     * PHP5-style constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');
    }

    /**
     * Returns true if minifier exists
     *
     * @param string $engine
     * @return boolean
     */
    function exists($engine) {
        return isset($this->_minifiers[$engine]);
    }

    /**
     * Returns true if minifier available
     *
     * @param string $engine
     * @return boolean
     */
    function available($engine) {
        switch ($engine) {
            case 'yuijs':
                $path_java = $this->_config->get_string('minify.yuijs.path.java');
                $path_jar = $this->_config->get_string('minify.yuijs.path.jar');

                return (file_exists($path_java) && file_exists($path_jar));

            case 'yuicss':
                $path_java = $this->_config->get_string('minify.yuicss.path.java');
                $path_jar = $this->_config->get_string('minify.yuicss.path.jar');

                return (file_exists($path_java) && file_exists($path_jar));

            case 'ccjs':
                $path_java = $this->_config->get_string('minify.ccjs.path.java');
                $path_jar = $this->_config->get_string('minify.ccjs.path.jar');

                return (file_exists($path_java) && file_exists($path_jar));

            case 'htmltidy':
            case 'htmltidyxml':
                return class_exists('tidy');

            case 'js':
            case 'css':
            case 'csstidy':
            case 'html':
            case 'htmlxml':
                return true;
        }

        return false;
    }

    /**
     * Returns minifier
     *
     * @param string $engine
     * @return array
     */
    function get_minifier($engine) {
        if (isset($this->_minifiers[$engine])) {
            return $this->_minifiers[$engine];
        }

        return null;
    }

    /**
     * Initializes minifier
     *
     * @param string $engine
     * @return void
     */
    function init($engine) {
        switch ($engine) {
            case 'js':
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/JSMin.php');
                break;

            case 'css':
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/CSS.php');
                break;

            case 'yuijs':
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/YUICompressor.php');

                Minify_YUICompressor::setPathJava($this->_config->get_string('minify.yuijs.path.java'));
                Minify_YUICompressor::setPathJar($this->_config->get_string('minify.yuijs.path.jar'));
                break;

            case 'yuicss':
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/YUICompressor.php');

                Minify_YUICompressor::setPathJava($this->_config->get_string('minify.yuicss.path.java'));
                Minify_YUICompressor::setPathJar($this->_config->get_string('minify.yuicss.path.jar'));
                break;

            case 'ccjs':
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/ClosureCompiler.php');

                Minify_ClosureCompiler::setPathJava($this->_config->get_string('minify.ccjs.path.java'));
                Minify_ClosureCompiler::setPathJar($this->_config->get_string('minify.ccjs.path.jar'));
                break;

            case 'csstidy':
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/CSSTidy.php');
                break;

            case 'html':
            case 'htmlxml':
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/HTML.php');
                break;

            case 'htmltidy':
            case 'htmltidyxml':
                w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/HTMLTidy.php');
                break;
        }
    }

    /**
     * Returns minifier options
     *
     * @param string $engine
     * @return array
     */
    function get_options($engine) {
        $options = array();

        switch ($engine) {
            case 'js':
                $options = array(
                    'preserveComments' => !$this->_config->get_boolean('minify.js.strip.comments'),
                    'stripCrlf' => $this->_config->get_boolean('minify.js.strip.crlf')
                );
                break;

            case 'css':
                $options = array(
                    'preserveComments' => !$this->_config->get_boolean('minify.css.strip.comments'),
                    'stripCrlf' => $this->_config->get_boolean('minify.css.strip.crlf')
                );

                $symlinks = $this->_config->get_array('minify.symlinks');

                foreach ($symlinks as $link => $target) {
                    $link = str_replace('//', realpath($_SERVER['DOCUMENT_ROOT']), $link);
                    $link = strtr($link, '/', DIRECTORY_SEPARATOR);
                    $options['symlinks'][$link] = realpath($target);
                }
                break;

            case 'yuijs':
                $options = array(
                    'line-break' => $this->_config->get_integer('minify.yuijs.options.line-break'),
                    'nomunge' => $this->_config->get_boolean('minify.yuijs.options.nomunge'),
                    'preserve-semi' => $this->_config->get_boolean('minify.yuijs.options.preserve-semi'),
                    'disable-optimizations' => $this->_config->get_boolean('minify.yuijs.options.disable-optimizations')
                );
                break;

            case 'yuicss':
                $options = array(
                    'line-break' => $this->_config->get_integer('minify.yuicss.options.line-break')
                );
                break;

            case 'ccjs':
                $options = array(
                    'compilation_level' => $this->_config->get_string('minify.ccjs.options.compilation_level'),
                    'formatting' => $this->_config->get_string('minify.ccjs.options.formatting')
                );
                break;

            case 'csstidy':
                $options = array(
                    'remove_bslash' => $this->_config->get_boolean('minify.csstidy.options.remove_bslash'),
                    'compress_colors' => $this->_config->get_boolean('minify.csstidy.options.compress_colors'),
                    'compress_font-weight' => $this->_config->get_boolean('minify.csstidy.options.compress_font-weight'),
                    'lowercase_s' => $this->_config->get_boolean('minify.csstidy.options.lowercase_s'),
                    'optimise_shorthands' => $this->_config->get_integer('minify.csstidy.options.optimise_shorthands'),
                    'remove_last_;' => $this->_config->get_boolean('minify.csstidy.options.remove_last_;'),
                    'case_properties' => $this->_config->get_integer('minify.csstidy.options.case_properties'),
                    'sort_properties' => $this->_config->get_boolean('minify.csstidy.options.sort_properties'),
                    'sort_selectors' => $this->_config->get_boolean('minify.csstidy.options.sort_selectors'),
                    'merge_selectors' => $this->_config->get_integer('minify.csstidy.options.merge_selectors'),
                    'discard_invalid_properties' => $this->_config->get_boolean('minify.csstidy.options.discard_invalid_properties'),
                    'css_level' => $this->_config->get_string('minify.csstidy.options.css_level'),
                    'preserve_css' => $this->_config->get_boolean('minify.csstidy.options.preserve_css'),
                    'timestamp' => $this->_config->get_boolean('minify.csstidy.options.timestamp'),
                    'template' => $this->_config->get_string('minify.csstidy.options.template')
                );
                break;

            case 'html':
            case 'htmlxml':
                $options = array(
                    'xhtml' => true,
                    'stripCrlf' => $this->_config->get_boolean('minify.html.strip.crlf'),
                    'ignoredComments' => $this->_config->get_array('minify.html.comments.ignore')
                );
                break;

            case 'htmltidy':
            case 'htmltidyxml':
                $options = array(
                    'clean' => $this->_config->get_boolean('minify.htmltidy.options.clean'),
                    'hide-comments' => $this->_config->get_boolean('minify.htmltidy.options.hide-comments'),
                    'wrap' => $this->_config->get_integer('minify.htmltidy.options.wrap')
                );
                break;
        }

        if ($this->_config->get_boolean('browsercache.enabled') && ($this->_config->get_boolean('browsercache.cssjs.replace') || $this->_config->get_boolean('browsercache.html.replace') || $this->_config->get_boolean('browsercache.other.replace'))) {
            $w3_plugin_browsercache = w3_instance('W3_Plugin_BrowserCache');

            $options = array_merge($options, array(
                'browserCacheId' => $w3_plugin_browsercache->get_replace_id(),
                'browserCacheExtensions' => $w3_plugin_browsercache->get_replace_extensions()
            ));
        }

        if ($this->_config->get_boolean('cdn.enabled') && $this->_config->get_boolean('cdn.minify.enable')) {
            $w3_plugin_cdn = w3_instance('W3_Plugin_CdnCommon');
            $cdn = $w3_plugin_cdn->get_cdn();

            $options = array_merge($options, array(
                'prependAbsolutePathCallback' => array(&$cdn, 'get_prepend_path'),
            ));
        }

        return $options;
    }
}
