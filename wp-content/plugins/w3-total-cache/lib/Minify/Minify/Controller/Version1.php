<?php
/**
 * Class Minify_Controller_Version1
 * @package Minify
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Controller/Base.php');

/**
 * Controller class for emulating version 1 of minify.php
 *
 * <code>
 * Minify::serve('Version1');
 * </code>
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_Controller_Version1 extends Minify_Controller_Base {

    /**
     * Set up groups of files as sources
     *
     * @param array $options controller and Minify options
     * @return array Minify options
     *
     */
    public function setupSources($options) {
        self::_setupDefines();
        if (MINIFY_USE_CACHE) {
            $cacheDir = defined('MINIFY_CACHE_DIR')
                ? MINIFY_CACHE_DIR
                : '';
            Minify::setCache($cacheDir);
        }
        $options['badRequestHeader'] = 'HTTP/1.0 404 Not Found';
        $options['contentTypeCharset'] = MINIFY_ENCODING;

        // The following restrictions are to limit the URLs that minify will
        // respond to. Ideally there should be only one way to reference a file.
        if (! isset($_GET['files'])
            // verify at least one file, files are single comma separated,
            // and are all same extension
            || ! preg_match('/^[^,]+\\.(css|js)(,[^,]+\\.\\1)*$/', $_GET['files'], $m)
            // no "//" (makes URL rewriting easier)
            || strpos($_GET['files'], '//') !== false
            // no "\"
            || strpos($_GET['files'], '\\') !== false
            // no "./"
            || preg_match('/(?:^|[^\\.])\\.\\//', $_GET['files'])
        ) {
            return $options;
        }
        $extension = $m[1];

        $files = explode(',', $_GET['files']);
        if (count($files) > MINIFY_MAX_FILES) {
            return $options;
        }

        // strings for prepending to relative/absolute paths
        $prependRelPaths = dirname($_SERVER['SCRIPT_FILENAME'])
            . DIRECTORY_SEPARATOR;
        $prependAbsPaths = $_SERVER['DOCUMENT_ROOT'];

        $sources = array();
        $goodFiles = array();
        $hasBadSource = false;

        $allowDirs = isset($options['allowDirs'])
            ? $options['allowDirs']
            : MINIFY_BASE_DIR;

        foreach ($files as $file) {
            // prepend appropriate string for abs/rel paths
            $file = ($file[0] === '/' ? $prependAbsPaths : $prependRelPaths) . $file;
            // make sure a real file!
            $file = realpath($file);
            // don't allow unsafe or duplicate files
            if (parent::_fileIsSafe($file, $allowDirs)
                && !in_array($file, $goodFiles))
            {
                $goodFiles[] = $file;
                $srcOptions = array(
                    'filepath' => $file
                );
                $this->sources[] = new Minify_Source($srcOptions);
            } else {
                $hasBadSource = true;
                break;
            }
        }
        if ($hasBadSource) {
            $this->sources = array();
        }
        if (! MINIFY_REWRITE_CSS_URLS) {
            $options['rewriteCssUris'] = false;
        }
        return $options;
    }

    private static function _setupDefines()
    {
        $defaults = array(
            'MINIFY_BASE_DIR' => realpath($_SERVER['DOCUMENT_ROOT'])
            ,'MINIFY_ENCODING' => 'utf-8'
            ,'MINIFY_MAX_FILES' => 16
            ,'MINIFY_REWRITE_CSS_URLS' => true
            ,'MINIFY_USE_CACHE' => true
        );
        foreach ($defaults as $const => $val) {
            if (! defined($const)) {
                define($const, $val);
            }
        }
    }
}

