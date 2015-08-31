<?php
/**
 * Class HTTP_Encoder  
 * @package Minify
 * @subpackage HTTP
 */
 
/**
 * Encode and send gzipped/deflated content
 *
 * The "Vary: Accept-Encoding" header is sent. If the client allows encoding, 
 * Content-Encoding and Content-Length are added.
 *
 * <code>
 * // Send a CSS file, compressed if possible
 * $he = new HTTP_Encoder(array(
 *     'content' => file_get_contents($cssFile)
 *     ,'type' => 'text/css'
 * ));
 * $he->encode();
 * $he->sendAll();
 * </code>
 *
 * <code>
 * // Shortcut to encoding output
 * header('Content-Type: text/css'); // needed if not HTML
 * HTTP_Encoder::output($css);
 * </code>
 * 
 * <code>
 * // Just sniff for the accepted encoding
 * $encoding = HTTP_Encoder::getAcceptedEncoding();
 * </code>
 *
 * For more control over headers, use getHeaders() and getData() and send your
 * own output.
 * 
 * Note: If you don't need header mgmt, use PHP's native gzencode, gzdeflate, 
 * and gzcompress functions for gzip, deflate, and compress-encoding
 * respectively.
 * 
 * @package Minify
 * @subpackage HTTP
 * @author Stephen Clay <steve@mrclay.org>
 */
class HTTP_Encoder {

    /**
     * Should the encoder allow HTTP encoding to IE6? 
     * 
     * If you have many IE6 users and the bandwidth savings is worth troubling 
     * some of them, set this to true.
     * 
     * By default, encoding is only offered to IE7+. When this is true,
     * getAcceptedEncoding() will return an encoding for IE6 if its user agent
     * string contains "SV1". This has been documented in many places as "safe",
     * but there seem to be remaining, intermittent encoding bugs in patched 
     * IE6 on the wild web.
     * 
     * @var bool
     */
    public static $encodeToIe6 = true;
    
    
    /**
     * Default compression level for zlib operations
     * 
     * This level is used if encode() is not given a $compressionLevel
     * 
     * @var int
     */
    public static $compressionLevel = 6;
    

    /**
     * Get an HTTP Encoder object
     * 
     * @param array $spec options
     * 
     * 'content': (string required) content to be encoded
     * 
     * 'type': (string) if set, the Content-Type header will have this value.
     * 
     * 'method: (string) only set this if you are forcing a particular encoding
     * method. If not set, the best method will be chosen by getAcceptedEncoding()
     * The available methods are 'gzip', 'deflate', 'compress', and '' (no
     * encoding)
     * 
     * @return null
     */
    public function __construct($spec) 
    {
        $this->_content = $spec['content'];
        $this->_headers['Content-Length'] = (string)strlen($this->_content);
        if (isset($spec['type'])) {
            $this->_headers['Content-Type'] = $spec['type'];
        }
        if (isset($spec['method'])
            && in_array($spec['method'], array('gzip', 'deflate', '')))
        {
            $this->_encodeMethod = array($spec['method'], $spec['method']);
        } else {
            $this->_encodeMethod = self::getAcceptedEncoding();
        }
    }

    /**
     * Get content in current form
     * 
     * Call after encode() for encoded content.
     * 
     * return string
     */
    public function getContent() 
    {
        return $this->_content;
    }
    
    /**
     * Get array of output headers to be sent
     * 
     * E.g.
     * <code>
     * array(
     *     'Content-Length' => '615'
     *     ,'Content-Encoding' => 'x-gzip'
     *     ,'Vary' => 'Accept-Encoding'
     * )
     * </code>
     *
     * @return array 
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Send output headers
     * 
     * You must call this before headers are sent and it probably cannot be
     * used in conjunction with zlib output buffering / mod_gzip. Errors are
     * not handled purposefully.
     * 
     * @see getHeaders()
     * 
     * @return null
     */
    public function sendHeaders()
    {
        foreach ($this->_headers as $name => $val) {
            header($name . ': ' . $val);
        }
    }
    
    /**
     * Send output headers and content
     * 
     * A shortcut for sendHeaders() and echo getContent()
     *
     * You must call this before headers are sent and it probably cannot be
     * used in conjunction with zlib output buffering / mod_gzip. Errors are
     * not handled purposefully.
     * 
     * @return null
     */
    public function sendAll()
    {
        $this->sendHeaders();
        echo $this->_content;
    }

    /**
     * Determine the client's best encoding method from the HTTP Accept-Encoding 
     * header.
     * 
     * If no Accept-Encoding header is set, or the browser is IE before v6 SP2,
     * this will return ('', ''), the "identity" encoding.
     * 
     * A syntax-aware scan is done of the Accept-Encoding, so the method must
     * be non 0. The methods are favored in order of gzip, deflate, then 
     * compress. Deflate is always smallest and generally faster, but is 
     * rarely sent by servers, so client support could be buggier.
     * 
     * @return array two values, 1st is the actual encoding method, 2nd is the
     * alias of that method to use in the Content-Encoding header (some browsers
     * call gzip "x-gzip" etc.)
     */
    public static function getAcceptedEncoding()
    {
        // @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
        
        if (! isset($_SERVER['HTTP_ACCEPT_ENCODING'])
            || w3_zlib_output_compression()
            || headers_sent()
            || self::_isBuggyIe())
        {
            return array('', '');
        }
        
        if (stristr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode')) {
            return array('gzip', 'gzip');
        }

        return array('', '');
    }
    
    /**
     * Encode (compress) the content
     * 
     * If the encode method is '' (none) or compression level is 0, or the 'zlib'
     * extension isn't loaded, we return false.
     * 
     * Then the appropriate gz_* function is called to compress the content. If
     * this fails, false is returned.
     * 
     * The header "Vary: Accept-Encoding" is added. If encoding is successful, 
     * the Content-Length header is updated, and Content-Encoding is also added.
     * 
     * @param int $compressionLevel given to zlib functions. If not given, the
     * class default will be used.
     * 
     * @return bool success true if the content was actually compressed
     */
    public function encode($compressionLevel = null)
    {
        $this->_headers['Vary'] = 'Accept-Encoding';
        if (null === $compressionLevel) {
            $compressionLevel = self::$compressionLevel;
        }
        if ('' === $this->_encodeMethod[0]
            || ($compressionLevel == 0)
            || !extension_loaded('zlib'))
        {
            return false;
        }
        if ($this->_encodeMethod[0] === 'deflate') {
            $encoded = gzdeflate($this->_content, $compressionLevel);
        } elseif ($this->_encodeMethod[0] === 'gzip') {
            $encoded = gzencode($this->_content, $compressionLevel);
        } else {
            $encoded = gzcompress($this->_content, $compressionLevel);
        }
        if (false === $encoded) {
            return false;
        }
        $this->_headers['Content-Length'] = strlen($encoded);
        $this->_headers['Content-Encoding'] = $this->_encodeMethod[1];
        $this->_content = $encoded;
        return true;
    }
    
    /**
     * Encode and send appropriate headers and content
     *
     * This is a convenience method for common use of the class
     * 
     * @param string $content
     * 
     * @param int $compressionLevel given to zlib functions. If not given, the
     * class default will be used.
     * 
     * @return bool success true if the content was actually compressed
     */
    public static function output($content, $compressionLevel = null)
    {
        if (null === $compressionLevel) {
            $compressionLevel = self::$compressionLevel;
        }
        $he = new HTTP_Encoder(array('content' => $content));
        $ret = $he->encode($compressionLevel);
        $he->sendAll();
        return $ret;
    }
    
    protected $_content = '';
    protected $_headers = array();
    protected $_encodeMethod = array('', '');

    /**
     * Is the browser an IE version earlier than 6 SP2?  
     */
    protected static function _isBuggyIe()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        // quick escape for non-IEs
        if (0 !== strpos($ua, 'Mozilla/4.0 (compatible; MSIE ')
            || false !== strpos($ua, 'Opera')) {
            return false;
        }
        // no regex = faaast
        $version = (float)substr($ua, 30); 
        return self::$encodeToIe6
            ? ($version < 6 || ($version == 6 && false === strpos($ua, 'SV1')))
            : ($version < 7);
    }
}
