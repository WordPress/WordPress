<?php
/**
 * Class Minify_Controller_MinApp
 * @package Minify
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Controller/Base.php');

/**
 * Controller class for requests to /min/index.php
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_Controller_MinApp extends Minify_Controller_Base {

    /**
     * Set up groups of files as sources
     *
     * @param array $options controller and Minify options
     * @return array Minify options
     *
     */
    public function setupSources($options) {
        // filter controller options
        $cOptions = array_merge(
            array(
                'allowDirs' => '//'
                ,'groupsOnly' => false
                ,'groups' => array()
                ,'maxFiles' => 100
            )
            ,(isset($options['minApp']) ? $options['minApp'] : array())
        );
        unset($options['minApp']);
        $sources = array();
        if (isset($_GET['g'])) {
            // try groups
            if (! isset($cOptions['groups'][$_GET['g']])) {
                $this->log("A group configuration for \"{$_GET['g']}\" was not set");
                return $options;
            }

            $files = $cOptions['groups'][$_GET['g']];
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
                    continue;
                }
            }
        } elseif (! $cOptions['groupsOnly'] && isset($_GET['f'])) {
            $config = w3_instance('W3_Config');
            $external = $config->get_array('minify.cache.files');

            $files = $_GET['f'];
            $temp_files = array();
            $external_files = 0;
            foreach ($files as $file) {
                if (!is_string($file)) {
                    $url = $file->minifyOptions['prependRelativePath'];
                    $verified  = false;
                    foreach($external as $ext) {
                        if(preg_match('#'.w3_get_url_regexp($ext).'#',$url) && !$verified){
                            $verified = true;
                        }
                    }
                    if (!$verified) {
                        $this->log("GET['f'] param part invalid, not in accepted external files list: \"{$url}\"");
                        return $options;
                    }
                    $external_files++;
                } else {
                    $temp_files[] = $file;
                }
            }

            if ($temp_files) {
                $imploded = implode(',', $temp_files);
                if (// verify at least one file, files are single comma separated,
                    // and are all same extension
                    ! preg_match('/^[^,]+\\.(css|js)(?:,[^,]+\\.\\1)*$/', $imploded)
                    // no "//"
                    || strpos($imploded, '//') !== false
                    // no "\"
                    || strpos($imploded, '\\') !== false
                    // no "./"
                    || preg_match('/(?:^|[^\\.])\\.\\//', $imploded)
                ) {
                    $this->log("GET['f'] param part invalid: \"{$imploded}\"");
                    return $options;
                }
            }

            if (count($files) > $cOptions['maxFiles'] || (count($files)-$external_files) != count(array_unique($temp_files))) {
                $this->log("Too many or duplicate files specified: \"" . implode(', ', $temp_files) . "\"");
                return $options;
            }
            if (!empty($_GET['b'])) {
                // check for validity
                if (preg_match('@^[^/]+(?:/[^/]+)*$@', $_GET['b'])
                    && false === strpos($_GET['b'], '..')
                    && $_GET['b'] !== '.') {
                    // valid base
                    $base = "/{$_GET['b']}/";
                } else {
                    $this->log("GET['b'] param invalid: \"{$_GET['b']}\"");
                    return $options;
                }
            } else {
                $base = '/';
            }
            $allowDirs = array();
            foreach ((array)$cOptions['allowDirs'] as $allowDir) {
                $allowDirs[] = realpath(str_replace('//', $_SERVER['DOCUMENT_ROOT'] . '/', $allowDir));
            }
            foreach ($files as $file) {
                if ($file instanceof Minify_Source) {
                    $sources[] = $file;
                    continue;
                }

                $path = $_SERVER['DOCUMENT_ROOT'] . $base . $file;
                $file = realpath($path);
                if (false === $file) {
                    $this->log("Path \"{$path}\" failed realpath()");
                    return $options;
                } elseif (! parent::_fileIsSafe($file, $allowDirs)) {
                    $this->log("Path \"{$path}\" failed Minify_Controller_Base::_fileIsSafe()");
                    return $options;
                } else {
                    $sources[] = new Minify_Source(array(
                        'filepath' => $file
                    ));
                }
            }
        }
        if ($sources) {
            $this->sources = $sources;
        } else {
            $this->log("No sources to serve");
        }
        return $options;
    }
}
