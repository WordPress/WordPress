<?php
/**
 * Class Minify_Build
 * @package Minify
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Source.php');

/**
 * Maintain a single last modification time for a group of Minify sources to
 * allow use of far off Expires headers in Minify.
 *
 * <code>
 * // in config file
 * $groupSources = array(
 *   'js' => array('file1.js', 'file2.js')
 *   ,'css' => array('file1.css', 'file2.css', 'file3.css')
 * )
 *
 * // during HTML generation
 * $jsBuild = new Minify_Build($groupSources['js']);
 * $cssBuild = new Minify_Build($groupSources['css']);
 *
 * $script = "<script type='text/javascript' src='"
 *     . $jsBuild->uri('/min.php/js') . "'></script>";
 * $link = "<link rel='stylesheet' type='text/css' href='"
 *     . $cssBuild->uri('/min.php/css') . "'>";
 *
 * // in min.php
 * Minify::serve('Groups', array(
 *   'groups' => $groupSources
 *   ,'setExpires' => (time() + 86400 * 365)
 * ));
 * </code>
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_Build {

    /**
     * Last modification time of all files in the build
     *
     * @var int
     */
    public $lastModified = 0;

    /**
     * String to use as ampersand in uri(). Set this to '&' if
     * you are not HTML-escaping URIs.
     *
     * @var string
     */
    public static $ampersand = '&amp;';

    /**
     * Get a time-stamped URI
     *
     * <code>
     * echo $b->uri('/site.js');
     * // outputs "/site.js?1678242"
     *
     * echo $b->uri('/scriptaculous.js?load=effects');
     * // outputs "/scriptaculous.js?load=effects&amp1678242"
     * </code>
     *
     * @param string $uri
     * @param boolean $forceAmpersand (default = false) Force the use of ampersand to
     * append the timestamp to the URI.
     * @return string
     */
    public function uri($uri, $forceAmpersand = false) {
        $sep = ($forceAmpersand || strpos($uri, '?') !== false)
            ? self::$ampersand
            : '?';
        return "{$uri}{$sep}{$this->lastModified}";
    }

	/**
     * Create a build object
     *
     * @param array $sources array of Minify_Source objects and/or file paths
     *
     * @return null
     */
    public function __construct($sources)
    {
        $max = 0;
        foreach ((array)$sources as $source) {
            if ($source instanceof Minify_Source) {
                $max = max($max, $source->lastModified);
            } elseif (is_string($source)) {
                if (0 === strpos($source, '//')) {
                    $source = $_SERVER['DOCUMENT_ROOT'] . substr($source, 1);
                }
                if (is_file($source)) {
                    $max = max($max, filemtime($source));
                }
            }
        }
        $this->lastModified = $max;
    }
}
