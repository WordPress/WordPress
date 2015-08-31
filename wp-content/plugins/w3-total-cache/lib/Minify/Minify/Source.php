<?php
/**
 * Class Minify_Source  
 * @package Minify
 */

/** 
 * A content source to be minified by Minify. 
 * 
 * This allows per-source minification options and the mixing of files with
 * content from other sources.
 * 
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_Source {

    /**
     * @var int time of last modification
     */
    public $lastModified = null;
    
    /**
     * @var callback minifier function specifically for this source.
     */
    public $minifier = null;
    
    /**
     * @var array minification options specific to this source.
     */
    public $minifyOptions = null;

    /**
     * @var string full path of file
     */
    public $filepath = null;
    
    /**
     * @var string HTTP Content Type (Minify requires one of the constants Minify::TYPE_*)
     */
    public $contentType = null;
    
    /**
     * Create a Minify_Source
     * 
     * In the $spec array(), you can either provide a 'filepath' to an existing
     * file (existence will not be checked!) or give 'id' (unique string for 
     * the content), 'content' (the string content) and 'lastModified' 
     * (unixtime of last update).
     * 
     * As a shortcut, the controller will replace "//" at the beginning
     * of a filepath with $_SERVER['DOCUMENT_ROOT'] . '/'.
     *
     * @param array $spec options
     */
    public function __construct($spec)
    {
        if (isset($spec['filepath'])) {
            if (0 === strpos($spec['filepath'], '//')) {
                $spec['filepath'] = $_SERVER['DOCUMENT_ROOT'] . substr($spec['filepath'], 1);
            }
            $segments = explode('.', $spec['filepath']);
            $ext = strtolower(array_pop($segments));
            switch ($ext) {
            case 'js'   : $this->contentType = 'application/x-javascript';
                          break;
            case 'css'  : $this->contentType = 'text/css';
                          break;
            case 'htm'  : // fallthrough
            case 'html' : $this->contentType = 'text/html';
                          break;
            }
            $this->filepath = $spec['filepath'];
            $this->_id = $spec['filepath'];
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify.php');
            $this->lastModified = filemtime($spec['filepath'])
                // offset for Windows uploaders with out of sync clocks
                + round(Minify::$uploaderHoursBehind * 3600);
        } elseif (isset($spec['id'])) {
            $this->_id = 'id::' . $spec['id'];
            if (isset($spec['content'])) {
                $this->_content = $spec['content'];
            } else {
                $this->_getContentFunc = $spec['getContentFunc'];
            }
            $this->lastModified = isset($spec['lastModified'])
                ? $spec['lastModified']
                : time();
        }
        if (isset($spec['contentType'])) {
            $this->contentType = $spec['contentType'];
        }
        if (isset($spec['minifier'])) {
            $this->minifier = $spec['minifier'];
        }
        if (isset($spec['minifyOptions'])) {
            $this->minifyOptions = $spec['minifyOptions'];
        }
    }
    
    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        if (isset($this->minifyOptions['processCssImports']) && $this->minifyOptions['processCssImports']) {
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/ImportProcessor.php');

            $content = Minify_ImportProcessor::process($this->filepath);
        } else {
            $content = (null !== $this->filepath)
                ? file_get_contents($this->filepath)
                : ((null !== $this->_content)
                    ? $this->_content
                    : call_user_func($this->_getContentFunc, $this->_id)
                );
        }
        
        // remove UTF-8 BOM if present
        return (pack("CCC",0xef,0xbb,0xbf) === substr($content, 0, 3))
            ? substr($content, 3)
            : $content;
    }
    
    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Verifies a single minification call can handle all sources
     *
     * @param array $sources Minify_Source instances
     * 
     * @return bool true iff there no sources with specific minifier preferences.
     */
    public static function haveNoMinifyPrefs($sources)
    {
        foreach ($sources as $source) {
            if (null !== $source->minifier
                || null !== $source->minifyOptions) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Get unique string for a set of sources
     *
     * @param array $sources Minify_Source instances
     * 
     * @return string
     */
    public static function getDigest($sources)
    {
        foreach ($sources as $source) {
            $info[] = array(
                $source->_id, $source->minifier, $source->minifyOptions
            );
        }
        return md5(serialize($info));
    }
    
    /**
     * Get content type from a group of sources
     * 
     * This is called if the user doesn't pass in a 'contentType' options  
     * 
     * @param array $sources Minify_Source instances
     * 
     * @return string content type. e.g. 'text/css'
     */
    public static function getContentType($sources)
    {
        foreach ($sources as $source) {
            if ($source->contentType !== null) {
                return $source->contentType;
            }
        }
        return 'text/plain';
    }
    
    protected $_content = null;
    protected $_getContentFunc = null;
    protected $_id = null;
}

