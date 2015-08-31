<?php
/**
 * Class Minify_Controller_Base  
 * @package Minify
 */

/**
 * Base class for Minify controller
 * 
 * The controller class validates a request and uses it to create sources
 * for minification and set options like contentType. It's also responsible
 * for loading minifier code upon request.
 * 
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
abstract class Minify_Controller_Base {
    
    /**
     * Setup controller sources and set an needed options for Minify::source
     * 
     * You must override this method in your subclass controller to set 
     * $this->sources. If the request is NOT valid, make sure $this->sources 
     * is left an empty array. Then strip any controller-specific options from 
     * $options and return it. To serve files, $this->sources must be an array of
     * Minify_Source objects.
     * 
     * @param array $options controller and Minify options
     * 
     * return array $options Minify::serve options
     */
    abstract public function setupSources($options);
    
    /**
     * Get default Minify options for this controller.
     * 
     * Override in subclass to change defaults
     *
     * @return array options for Minify
     */
    public function getDefaultMinifyOptions() {
        return array(
            'isPublic' => true
            ,'encodeOutput' => function_exists('gzdeflate')
            ,'encodeMethod' => null // determine later
            ,'encodeLevel' => 9
            ,'minifierOptions' => array() // no minifier options
            ,'contentTypeCharset' => 'utf-8'
            ,'maxAge' => 1800 // 30 minutes
            ,'rewriteCssUris' => true
            ,'bubbleCssImports' => false
            ,'processCssImports' => false
            ,'quiet' => false // serve() will send headers and output
            ,'debug' => false
            
            // if you override this, the response code MUST be directly after 
            // the first space.
            ,'badRequestHeader' => 'HTTP/1.0 400 Bad Request'
            
            // callback function to see/modify content of all sources
            ,'postprocessor' => null
            // file to require to load preprocessor
            ,'postprocessorRequire' => null
        );
    }  

    /**
     * Get default minifiers for this controller.
     * 
     * Override in subclass to change defaults
     *
     * @return array minifier callbacks for common types
     */
    public function getDefaultMinifers() {
        $ret[Minify::TYPE_JS] = array('JSMin', 'minify');
        $ret[Minify::TYPE_CSS] = array('Minify_CSS', 'minify');
        $ret[Minify::TYPE_HTML] = array('Minify_HTML', 'minify');
        return $ret;
    }
    
    /**
     * Load any code necessary to execute the given minifier callback.
     * 
     * The controller is responsible for loading minification code on demand
     * via this method. This built-in function will only load classes for
     * static method callbacks where the class isn't already defined. It uses
     * the PEAR convention, so, given array('Jimmy_Minifier', 'minCss'), this 
     * function will include 'Jimmy/Minifier.php'.
     * 
     * If you need code loaded on demand and this doesn't suit you, you'll need
     * to override this function in your subclass. 
     * @see Minify_Controller_Page::loadMinifier()
     * 
     * @param callback $minifierCallback callback of minifier function
     * 
     * @return null
     */
    public function loadMinifier($minifierCallback)
    {
        if (is_array($minifierCallback)
            && is_string($minifierCallback[0])
            && !class_exists($minifierCallback[0], false)) {
            
            require W3TC_LIB_MINIFY_DIR . '/' . str_replace('_', '/', $minifierCallback[0]) . '.php';
        }
    }
    
    /**
     * Is a user-given file within an allowable directory, existing,
     * and having an extension js/css/html/txt ?
     * 
     * This is a convenience function for controllers that have to accept
     * user-given paths
     *
     * @param string $file full file path (already processed by realpath())
     * 
     * @param array $safeDirs directories where files are safe to serve. Files can also
     * be in subdirectories of these directories.
     * 
     * @return bool file is safe
     */
    public static function _fileIsSafe($file, $safeDirs)
    {
        $pathOk = false;
        foreach ((array)$safeDirs as $safeDir) {
            if (strpos($file, $safeDir) === 0) {
                $pathOk = true;
                break;
            }
        }
        $base = basename($file);
        if (! $pathOk || ! is_file($file) || $base[0] === '.') {
            return false;
        }
        list($revExt) = explode('.', strrev($base));
        return in_array(strrev($revExt), array('js', 'css', 'html', 'txt'));
    }
    
    /**
     * @var array instances of Minify_Source, which provide content and
     * any individual minification needs.
     * 
     * @see Minify_Source
     */
    public $sources = array();
    
    /**
     * Mix in default controller options with user-given options
     * 
     * @param array $options user options
     * 
     * @return array mixed options
     */
    public final function mixInDefaultOptions($options)
    {
        $ret = array_merge(
            $this->getDefaultMinifyOptions(), $options
        );
        if (! isset($options['minifiers'])) {
            $options['minifiers'] = array();
        }
        $ret['minifiers'] = array_merge(
            $this->getDefaultMinifers(), $options['minifiers']
        );
        return $ret;
    }
    
    /**
     * Analyze sources (if there are any) and set $options 'contentType' 
     * and 'lastModifiedTime' if they already aren't.
     * 
     * @param array $options options for Minify
     * 
     * @return array options for Minify
     */
    public final function analyzeSources($options = array()) 
    {
        if ($this->sources) {
            if (! isset($options['contentType'])) {
                $options['contentType'] = Minify_Source::getContentType($this->sources);
            }
            // last modified is needed for caching, even if setExpires is set
            if (! isset($options['lastModifiedTime'])) {
                $max = 0;
                foreach ($this->sources as $source) {
                    $max = max($source->lastModified, $max);
                }
                $options['lastModifiedTime'] = $max;
            }    
        }
        return $options;
    }

    /**
     * Send message to the Minify logger
     * @param string $msg
     * @return null
     */
    protected function log($msg) {
        w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Logger.php');
        Minify_Logger::log($msg);
    }
}
