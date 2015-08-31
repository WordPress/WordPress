<?php
/**
 * Class Minify_Controller_Groups
 * @package Minify
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Controller/Base.php');

/**
 * Controller class for serving predetermined groups of minimized sets, selected
 * by PATH_INFO
 *
 * <code>
 * Minify::serve('Groups', array(
 *     'groups' => array(
 *         'css' => array('//css/type.css', '//css/layout.css')
 *        ,'js' => array('//js/jquery.js', '//js/site.js')
 *     )
 * ));
 * </code>
 *
 * If the above code were placed in /serve.php, it would enable the URLs
 * /serve.php/js and /serve.php/css
 *
 * As a shortcut, the controller will replace "//" at the beginning
 * of a filename with $_SERVER['DOCUMENT_ROOT'] . '/'.
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_Controller_Groups extends Minify_Controller_Base {

    /**
     * Set up groups of files as sources
     *
     * @param array $options controller and Minify options
     * @return array Minify options
     *
     * Controller options:
     *
     * 'groups': (required) array mapping PATH_INFO strings to arrays
     * of complete file paths. @see Minify_Controller_Groups
     */
    public function setupSources($options) {
        // strip controller options
        $groups = $options['groups'];
        unset($options['groups']);

        // mod_fcgid places PATH_INFO in ORIG_PATH_INFO
        $pi = isset($_SERVER['ORIG_PATH_INFO'])
            ? substr($_SERVER['ORIG_PATH_INFO'], 1)
            : (isset($_SERVER['PATH_INFO'])
                ? substr($_SERVER['PATH_INFO'], 1)
                : false
            );
        if (false === $pi || ! isset($groups[$pi])) {
            // no PATH_INFO or not a valid group
            $this->log("Missing PATH_INFO or no group set for \"$pi\"");
            return $options;
        }
        $sources = array();

        $files = $groups[$pi];
        // if $files is a single object, casting will break it
        if (is_object($files)) {
            $files = array($files);
        } elseif (! is_array($files)) {
            $files = (array)$files;
        }
        foreach ($files as $file) {
            if ($file instanceof Minify_Source) {
                $sources[] = $file;
                continue;
            }
            if (0 === strpos($file, '//')) {
                $file = $_SERVER['DOCUMENT_ROOT'] . substr($file, 1);
            }
            $realPath = realpath($file);
            if (is_file($realPath)) {
                $sources[] = new Minify_Source(array(
                    'filepath' => $realPath
                ));
            } else {
                $this->log("The path \"{$realPath}\" could not be found (or was not a file)");
                return $options;
            }
        }
        if ($sources) {
            $this->sources = $sources;
        }
        return $options;
    }
}

