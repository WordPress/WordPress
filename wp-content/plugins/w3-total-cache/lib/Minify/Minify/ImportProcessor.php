<?php
/**
 * Class Minify_ImportProcessor  
 * @package Minify
 */

/**
 * Linearize a CSS/JS file by including content specified by CSS import
 * declarations. In CSS files, relative URIs are fixed.
 * 
 * @imports will be processed regardless of where they appear in the source 
 * files; i.e. @imports commented out or in string content will still be
 * processed!
 * 
 * This has a unit test but should be considered "experimental".
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_ImportProcessor {
    
    public static $filesIncluded = array();
    
    public static function process($file)
    {
        self::$filesIncluded = array();
        self::$_isCss = (strtolower(substr($file, -4)) === '.css');
        $obj = new Minify_ImportProcessor(dirname($file));
        return $obj->_getContent($file);
    }
    
    // allows callback funcs to know the current directory
    private $_currentDir = null;
    
    // allows _importCB to write the fetched content back to the obj
    private $_importedContent = '';
    
    private static $_isCss = null;
    
    private function __construct($currentDir)
    {
        $this->_currentDir = $currentDir;
    }
    
    private function _getContent($file)
    {
        $file = realpath($file);
        if (! $file
            || in_array($file, self::$filesIncluded)
            || false === ($content = @file_get_contents($file))
        ) {
            // file missing, already included, or failed read
            return '';
        }
        self::$filesIncluded[] = realpath($file);
        $this->_currentDir = dirname($file);
        
        // remove UTF-8 BOM if present
        if (pack("CCC",0xef,0xbb,0xbf) === substr($content, 0, 3)) {
            $content = substr($content, 3);
        }
        // ensure uniform EOLs
        $content = str_replace("\r\n", "\n", $content);
        
        // process @imports
        $content = preg_replace_callback(
            '/
                @import\\s+
                (?:url\\(\\s*)?      # maybe url(
                [\'"]?               # maybe quote
                (.*?)                # 1 = URI
                [\'"]?               # maybe end quote
                (?:\\s*\\))?         # maybe )
                ([a-zA-Z,\\s]*)?     # 2 = media list
                ;                    # end token
            /x'
            ,array($this, '_importCB')
            ,$content
        );
        
        if (self::$_isCss) {
            // rewrite remaining relative URIs
            $content = preg_replace_callback(
                '/url\\(\\s*([^\\)\\s]+)\\s*\\)/'
                ,array($this, '_urlCB')
                ,$content
            );
        }
        
        return $this->_importedContent . $content;
    }
    
    private function _importCB($m)
    {
        $url = $m[1];
        $mediaList = preg_replace('/\\s+/', '', $m[2]);
        
        if (strpos($url, '://') > 0) {
            // protocol, leave in place for CSS, comment for JS
            return self::$_isCss
                ? $m[0]
                : "/* Minify_ImportProcessor will not include remote content */";
        }
        if ('/' === $url[0]) {
            // protocol-relative or root path
            $url = ltrim($url, '/');
            $file = realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR
                . strtr($url, '/', DIRECTORY_SEPARATOR);
        } else {
            // relative to current path
            $file = $this->_currentDir . DIRECTORY_SEPARATOR 
                . strtr($url, '/', DIRECTORY_SEPARATOR);
        }
        $obj = new Minify_ImportProcessor(dirname($file));
        $content = $obj->_getContent($file);
        if ('' === $content) {
            // failed. leave in place for CSS, comment for JS
            return self::$_isCss
                ? $m[0]
                : "/* Minify_ImportProcessor could not fetch '{$file}' */";
        }
        return (!self::$_isCss || preg_match('@(?:^$|\\ball\\b)@', $mediaList))
            ? $content
            : "@media {$mediaList} {\n{$content}\n}\n";
    }
    
    private function _urlCB($m)
    {
        // $m[1] is either quoted or not
        $quote = ($m[1][0] === "'" || $m[1][0] === '"')
            ? $m[1][0]
            : '';
        $url = ($quote === '')
            ? $m[1]
            : substr($m[1], 1, strlen($m[1]) - 2);
        if ('/' !== $url[0]) {
            if (false === strpos($url, '//')  // protocol (non-data)
                && 0 !== strpos($url, 'data:')) {  // data protocol
                // prepend path with current dir separator (OS-independent)
                $path = $this->_currentDir 
                    . DIRECTORY_SEPARATOR . strtr($url, '/', DIRECTORY_SEPARATOR);
                // strip doc root
                $path = substr($path, strlen(realpath($_SERVER['DOCUMENT_ROOT'])));
                // fix to absolute URL
                $url = strtr($path, '/\\', '//');
                // remove /./ and /../ where possible
                $url = str_replace('/./', '/', $url);
                // inspired by patch from Oleg Cherniy
                do {
                    $url = preg_replace('@/[^/]+/\\.\\./@', '/', $url, 1, $changed);
                } while ($changed);
            }
        }
        return "url({$quote}{$url}{$quote})";
    }
}
