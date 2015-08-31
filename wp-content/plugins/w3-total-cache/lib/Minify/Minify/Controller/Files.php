<?php
/**
 * Class Minify_Controller_Files
 * @package Minify
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Controller/Base.php');

/**
 * Controller class for minifying a set of files
 *
 * E.g. the following would serve the minified Javascript for a site
 * <code>
 * Minify::serve('Files', array(
 *     'files' => array(
 *         '//js/jquery.js'
 *         ,'//js/plugins.js'
 *         ,'/home/username/file.js'
 *     )
 * ));
 * </code>
 *
 * As a shortcut, the controller will replace "//" at the beginning
 * of a filename with $_SERVER['DOCUMENT_ROOT'] . '/'.
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_Controller_Files extends Minify_Controller_Base {

    /**
     * Set up file sources
     *
     * @param array $options controller and Minify options
     * @return array Minify options
     *
     * Controller options:
     *
     * 'files': (required) array of complete file paths, or a single path
     */
    public function setupSources($options) {
        // strip controller options

        $files = $options['files'];
        // if $files is a single object, casting will break it
        if (is_object($files)) {
            $files = array($files);
        } elseif (! is_array($files)) {
            $files = (array)$files;
        }
        unset($options['files']);

        $sources = array();
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

